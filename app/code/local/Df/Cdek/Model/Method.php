<?php
/**
 * @method Df_Cdek_Model_Config_Facade getRmConfig()
 */
abstract class Df_Cdek_Model_Method extends Df_Shipping_Model_Method {
	/**
	 * @abstract
	 * @return bool
	 */
	abstract protected function needDeliverToHome();

	/**
	 * @override
	 * @return float
	 */
	public function getCost() {
		if (!isset($this->{__METHOD__})) {
			if (0 === $this->getCostInRoubles()) {
				$this->throwExceptionInvalidDestination();
			}
			$this->{__METHOD__} = $this->convertFromRoublesToBase($this->getCostInRoubles());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	public function getMethodTitle() {
		return rm_concat_clean(' '
			,rm_if($this->needShowMethodName(), parent::getMethodTitle())
			,rm_if(
				$this->needShowMethodName()
				,rm_sprintf(
					'(%s):', rm_first(df_a(self::$services, $this->getApi()->getServiceId()))
				)
			)
			,$this->formatTimeOfDelivery(
					$this->getApi()->getDeliveryTimeMin()
				+
					$this->getRmConfig()->admin()->getProcessingBeforeShippingDays()
				,
					$this->getApi()->getDeliveryTimeMax()
				+
					$this->getRmConfig()->admin()->getProcessingBeforeShippingDays()
			)
		);
	}

	/**
	 * @override
	 * @return bool
	 * @throws Exception
	 */
	public function isApplicable() {
		/** @var bool $result */
		$result = parent::isApplicable();
		if ($result) {
			try {
				$this
					->checkCityOriginIsNotEmpty()
					->checkCityDestinationIsNotEmpty()
				;
				if (!$this->getLocationIdOrigin()) {
					$this->throwExceptionInvalidOrigin();
				}
				if (!$this->getLocationIdDestination()) {
					$this->throwExceptionInvalidDestination();
				}
			}
			catch(Exception $e) {
				if ($this->needDisplayDiagnosticMessages()) {throw $e;} else {$result = false;}
			}
		}
		return $result;
	}

	/**
	 * @override
	 * @param string|null $locationName
	 * @param bool $isDestination[optional]
	 * @return int|null
	 */
	protected function getLocationIdByName($locationName, $isDestination = true) {
		return $this->getRequest()->getLocator($isDestination)->getResult();
	}

	/** @return Df_Cdek_Model_Request_Rate */
	private function getApi() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Cdek_Model_Request_Rate::i($this->getPostParams());
		}
		return $this->{__METHOD__};
	}
	
	/** @return float */
	private function getCostInRoubles() {
		if (!isset($this->{__METHOD__})) {
			/** @var float $result */
			$result = $this->getApi()->getRate();
			df_assert_float($result);
			if ($this->getRmConfig()->service()->needAcceptCashOnDelivery()) {
				/**
				 * @link http://www.edostavka.ru/nalozhennyj-platezh/
				 */
				/** @var float $factor */
				$factor = 1;
				if ($result < 500000) {
					$factor = 1.03;
				}
				else if ($result < 1000000) {
					$factor = 1.025;
				}
				else if ($result < 3000000) {
					$factor = 1.02;
				}
				else {
					$factor = 1.015;
				}
				$result *= $factor;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	private function getDeliveryType() {
		return
			$this->getRmConfig()->service()->needGetCargoFromTheShopStore()
			? ($this->needDeliverToHome() ? 1 : 2)
			: ($this->needDeliverToHome() ? 3 : 4)
		;
	}

	/** @return array(string => mixed) */
	private function getPostParams() {
		/** @var array(string => mixed) $result */
		$result =
			array(
				'version' => '1.0'
				,'senderCityId' => $this->getLocationIdOrigin()
				,'receiverCityId' => $this->getLocationIdDestination()
//				,
//				'modeId' => $this->getDeliveryType()
				,'tariffList' => $this->getServices($this->getDeliveryType())
				,'goods' => $this->getQuoteItemsDescriptionForShippingService()
			)
		;
		if (
				$this->getRmConfig()->service()->getShopId()
			&&
				$this->getRmConfig()->service()->getShopPassword()
		) {
			/** @var string $dateAsString */
			$dateAsString = df_dts(df()->date()->tomorrow(), 'yyyy-MM-dd');
			$result =
				array_merge(
					$result
					,array(
						'authLogin' => $this->getRmConfig()->service()->getShopId()
						,'secure' =>
							md5(
								implode(
									'&'
									,array(
										$dateAsString
										,$this->getRmConfig()->service()->getShopPassword()
									)
								)
							)
						/**
						 * «Дата планируемой отправки dateExecute не обязательна
						 * (в этом случае принимается текущая дата).
						 * Но, если Вы работаете с авторизацией,
						 * она должна быть обязательно передана,
						 * т. к. дата учитывается при шифровании/дешифровке пароля.»
						 */,'dateExecute' => $dateAsString
					)
				)
			;
		}
		return $result;
	}

	/** @return array(array(string => float|int)) */
	private function getQuoteItemsDescriptionForShippingService() {
		/** @var array(array(string => float|int)) $result */
		$result = array();
		foreach ($this->getRequest()->getQuoteItemsSimple() as $quoteItem) {
			/** @var Mage_Sales_Model_Quote_Item $quoteItem */
			/** @var Df_Catalog_Model_Product $product */
			$product =
				$this->getRequest()->getProductsWithDimensions()->getItemById(
					$quoteItem->getProductId()
				)
			;
			df_assert($product instanceof Df_Catalog_Model_Product);
			/** @var array $productEntry */
			$productEntry =
				array_merge(
					df_array_combine(
						array('width', 'height', 'length')
						,array_map(
							'ceil'
							,array_map(
								array(df()->units()->length(), 'convertToCentimetres')
								,array(
									$product->getWidth()
									,$product->getHeight()
									,$product->getLength()
								)
							)
						)
					)
					,array(
						'weight' =>
							df()->units()->weight()->convertToKilogrammes(
								// Здесь нужен вес именно товара, а не строки заказа
								rm_float(
									!$product->getWeight()
									? df_cfg()->shipping()->product()->getDefaultWeight()
									: $product->getWeight()
								)
							)
					)
				)
			;
			/** @var int $qty */
			$qty = Df_Sales_Model_Quote_Item_Extended::i($quoteItem)->getQty();
			for($productIndex = 0; $productIndex < $qty; $productIndex++) {
				$result[]= $productEntry;
			}
		}
		return $result;
	}

	/**
	 * @param int $deliveryType
	 * @return array
	 */
	private function getServices($deliveryType) {
		df_param_integer($deliveryType, 0);
		/** @var array $result */
		$result = array();
		/** @var int $priority */
		$priority = 1;
		foreach (self::$services as $serviceId => $serviceData) {
			/** @var int $serviceId */
			df_assert_integer($serviceId);
			/** @var array $serviceData */
			df_assert_array($serviceData);
			/** @var int $currentDeliveryType */
			$currentDeliveryType = rm_last($serviceData);
			df_assert_integer($currentDeliveryType);
			if ($deliveryType === $currentDeliveryType) {
				$result[]=
					array(
						'priority' => $priority++
						,'id' => $serviceId
					)
				;
			}

		}
		df_result_array($result);
		return $result;
	}

	const _CLASS = __CLASS__;
	/** @var array */
	private static $services =
		array(
			1 => array('Экспресс лайт дверь-дверь', 1)
			,3 => array('Супер-экспресс до 18', 1)
			,4 => array('Рассылка', 1)
			,5 => array('Экономичный экспресс склад-склад', 4)
			,7 => array('Международный экспресс документы', 1)
			,8 => array('Международный экспресс грузы', 1)
			,10 => array('Экспресс лайт склад-склад', 4)
			,11 => array('Экспресс лайт склад-дверь', 3)
			,12 => array('Экспресс лайт дверь-склад', 2)
			,15 => array('Экспресс тяжеловесы склад-склад', 4)
			,16 => array('Экспресс тяжеловесы склад-дверь', 3)
			,17 => array('Экспресс тяжеловесы дверь-склад', 2)
			,18 => array('Экспресс тяжеловесы дверь-дверь', 1)
			,57 => array('Супер-экспресс до 9', 1)
			,58 => array('Супер-экспресс до 10', 1)
			,59 => array('Супер-экспресс до 12', 1)
			,60 => array('Супер-экспресс до 14', 1)
			,61 => array('Супер-экспресс до 16', 1)
			,62 => array('Магистральный экспресс склад-склад', 4)
			,63 => array('Магистральный супер-экспресс склад-склад', 4)
			,66 => array('Блиц-экспресс 01', 1)
			,67 => array('Блиц-экспресс 02', 1)
			,68 => array('Блиц-экспресс 03', 1)
			,69 => array('Блиц-экспресс 04', 1)
			,70 => array('Блиц-экспресс 05', 1)
			,71 => array('Блиц-экспресс 06', 1)
			,72 => array('Блиц-экспресс 07', 1)
			,73 => array('Блиц-экспресс 08', 1)
			,74 => array('Блиц-экспресс 09', 1)
			,75 => array('Блиц-экспресс 10', 1)
			,76 => array('Блиц-экспресс 11', 1)
			,77 => array('Блиц-экспресс 12', 1)
			,78 => array('Блиц-экспресс 13', 1)
			,79 => array('Блиц-экспресс 14', 1)
			,80 => array('Блиц-экспресс 15', 1)
			,81 => array('Блиц-экспресс 16', 1)
			,82 => array('Блиц-экспресс 17', 1)
			,83 => array('Блиц-экспресс 18', 1)
			,84 => array('Блиц-экспресс 19', 1)
			,85 => array('Блиц-экспресс 20', 1)
			,86 => array('Блиц-экспресс 21', 1)
			,87 => array('Блиц-экспресс 22', 1)
			,88 => array('Блиц-экспресс 23', 1)
			,89 => array('Блиц-экспресс 24', 1)
			,136 => array('Посылка склад-склад', 4)
			,137 => array('Посылка склад-дверь', 3)
			,138 => array('Посылка дверь-склад', 2)
			,139 => array('Посылка дверь-дверь', 1)
			,140 => array('Возврат склад-склад', 4)
			,141 => array('Возврат склад-дверь', 3)
			,142 => array('Возврат дверь-склад', 2)
		)
	;
}