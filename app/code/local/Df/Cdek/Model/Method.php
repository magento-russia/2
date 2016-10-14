<?php
/** @method Df_Cdek_Model_Config_Area_Service configS() */
abstract class Df_Cdek_Model_Method extends Df_Shipping_Model_Method_Russia {
	/**
	 * @abstract
	 * @return bool
	 */
	abstract protected function needDeliverToHome();

	/**
	 * @override
	 * @return void
	 * @throws Exception
	 */
	protected function checkApplicability() {
		parent::checkApplicability();
		$this
			->checkCityOriginIsNotEmpty()
			->checkCityDestinationIsNotEmpty()
			->checkLocationIdOrigin()
			->checkLocationIdDestination()
		;
	}

	/**
	 * @override
	 * @used-by Df_Shipping_Model_Method::_getCost()
	 * @return float
	 */
	protected function getCost() {
		/** @var float $result */
		$result = $this->getApi()->getRate();
		if (!$result) {
			$this->throwExceptionInvalidDestination();
		}
		df_assert_float($result);
		if ($this->configS()->needAcceptCashOnDelivery()) {
			/** http://www.edostavka.ru/nalozhennyj-platezh/ */
			/** @var float $factor */
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
		return $result;
	}

	/**
	 * @override
	 * @used-by Df_Shipping_Model_Method::_getDeliveryTime()
	 * @return int|int[]
	 */
	protected function getDeliveryTime() {
		return array($this->getApi()->getDeliveryTimeMin(), $this->getApi()->getDeliveryTimeMax());
	}

	/**
	 * @override
	 * @param string|null $locationName
	 * @param bool $isDestination [optional]
	 * @return int|null
	 */
	protected function getLocationIdByName($locationName, $isDestination = true) {
		return $this->rr()->getLocator($isDestination)->getResult();
	}

	/**
	 * @override
	 * @used-by Df_Shipping_Model_Method::_getName()
	 * @return string
	 */
	protected function getName() {
		return rm_first(df_a(self::$services, $this->getApi()->getServiceId()));
	}

	/** @return Df_Cdek_Model_Request_Rate */
	private function getApi() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Cdek_Model_Request_Rate::i($this->getPostParams());
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	private function getDeliveryType() {
		return
			$this->configS()->needGetCargoFromTheShopStore()
			? ($this->needDeliverToHome() ? 1 : 2)
			: ($this->needDeliverToHome() ? 3 : 4)
		;
	}

	/** @return array(string => mixed) */
	private function getPostParams() {
		/** @var array(string => mixed) $result */
		$result = array(
			'version' => '1.0'
			,'senderCityId' => $this->getLocationIdOrigin()
			,'receiverCityId' => $this->getLocationIdDestination()
			,'tariffList' => $this->getServices($this->getDeliveryType())
			,'goods' => $this->getQuoteItemsDescriptionForShippingService()
		);
		if ($this->configS()->getShopId() && $this->configS()->getShopPassword()) {
			/** @var string $dateAsString */
			$dateAsString = df_dts(df()->date()->tomorrow(), 'yyyy-MM-dd');
			$result = array_merge($result, array(
				'authLogin' => $this->configS()->getShopId()
				,'secure' => md5(implode('&', array($dateAsString, $this->configS()->getShopPassword())))
				/**
				 * «Дата планируемой отправки dateExecute не обязательна
				 * (в этом случае принимается текущая дата).
				 * Но, если Вы работаете с авторизацией,
				 * она должна быть обязательно передана,
				 * т. к. дата учитывается при шифровании/дешифровке пароля.»
				 */
				,'dateExecute' => $dateAsString
			));
		}
		return $result;
	}

	/** @return array(array(string => float|int)) */
	private function getQuoteItemsDescriptionForShippingService() {
		/** @var array(array(string => float|int)) $result */
		$result = array();
		foreach ($this->rr()->getQuoteItemsSimple() as $quoteItem) {
			/** @var Mage_Sales_Model_Quote_Item $quoteItem */
			/** @var Df_Catalog_Model_Product $product */
			$product = $this->rr()->getProductsWithDimensions()->getItemById(
				$quoteItem->getProductId()
			);
			df_assert($product);
			/** @var float $weight */
			$weight = $product->getWeightInKilogrammes();
			/**
			 * 2015-01-25
			 * Обратите внимание,
			 * что для некоторых типов (например, виртуальных и скачиваемых) нормально не иметь вес.
			 * Передавать такие товары в службу доставку не нужно.
			 * Обратите также внимание,
			 * что если товар по своему типу (например, простой или настраиваемый) способен иметь вес,
			 * но информация о весе данного в базе данных интернет-магазина остутствует,
			 * то @uses Df_Catalog_Model_Product::getWeight()
			 * вернёт не нулевой вес, а то значение, которое администратор интернет-магазина
			 * указал в качестве веса по умолчанию.
			 */
			if ($weight) {
				/** @var array $productEntry */
				$productEntry = array_merge(
					df_array_combine(
						array('width', 'height', 'length')
						,array_map('ceil', rm_length()->inCentimetres(
							$product->getWidth(), $product->getHeight(), $product->getLength()
						))
					)
					// Здесь нужен вес именно товара, а не строки заказа
					,array('weight' => $weight)
				);
				/** @var int $qty */
				$qty = Df_Sales_Model_Quote_Item_Extended::i($quoteItem)->getQty();
				for ($productIndex = 0; $productIndex < $qty; $productIndex++) {
					$result[]= $productEntry;
				}
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
				$result[]= array('priority' => $priority++, 'id' => $serviceId);
			}
		}
		return $result;
	}

	/** @var array */
	private static $services = array(
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
	);
}