<?php
class Df_Pec_Model_Api_Calculator extends Df_Core_Model {
	/**
	 * @return array(string => array(string => int|float))
	 * @throws Exception
	 */
	public function getRates() {
		if (!isset($this->{__METHOD__})) {
			/** @var mixed[] $responseAsArray */
			$responseAsArray = $this->getApiRequest()->response()->json();
			try {
				/** @var array(string => array(string => int|float)) $result */
				$result = array();
				/** @var float[] $add */
				$add = df_a($responseAsArray, 'ADD', array());
				df_assert_array($add);
				/** @var float $addRate */
				$addRate = rm_float(df_a($add, 2));
				/** @var float[] $take */
				$take = df_a($responseAsArray, 'take', array());
				df_assert_array($take);
				/** @var float $takeRate */
				$takeRate = rm_float(df_a($take, 2));
				/** @var float[] $deliver */
				$deliver = df_a($responseAsArray, 'deliver', array());
				df_assert_array($deliver);
				/** @var float $deliverRate */
				$deliverRate = rm_float(df_a($deliver, 2));
				/** @var array|null $avia */
				$avia = df_a($responseAsArray, 'avia');
				if (!is_null($avia)) {
					df_assert_array($avia);
					/** @var float $airBaseRate */
					$airBaseRate = rm_float(df_a($avia, 2));
					/** @var array $methodAir */
					$methodAir =
						array(
							self::RESULT__RATE => $airBaseRate + $takeRate + $deliverRate  + $addRate
							,self::RESULT__DELIVERY_TIME_MIN => 2
							,self::RESULT__DELIVERY_TIME_MAX => 3
						)
					;
					$result[Df_Pec_Model_Method_Air::METHOD]= $methodAir;
				}
				/** @var float[]|null $auto */
				$auto = df_a($responseAsArray, 'auto');
				if (!is_null($auto)) {
					df_assert_array($auto);
					/** @var float $airBaseRate */
					$autoBaseRate = rm_float(df_a($auto, 2));
					df_assert_float($autoBaseRate);
					/** @var array(string => string) $methodAuto */
					$methodAuto =
						array(
							self::RESULT__RATE => $autoBaseRate + $takeRate + $deliverRate + $addRate
						)
					;
					/** @var string $deliveryTimeAsText */
					$deliveryTimeAsText = df_a($responseAsArray, 'periods');
					df_assert_string($deliveryTimeAsText);
					/** @var string $deliveryTimeAsTextWithoutTags */
					$deliveryTimeAsTextWithoutTags = strip_tags($deliveryTimeAsText);
					df_assert_string($deliveryTimeAsTextWithoutTags);
					/** @var int $deliveryTime */
					$deliveryTime =
						rm_preg_match_int(
							'#Количество суток в пути: (\d+)#u'
							, $deliveryTimeAsTextWithoutTags
							, false
						)
					;
					/**
					 * Иногда ПЭК не указывает сроки, но тарифы выдаёт.
					 * Заметил такое для перевозки Москва => Москва (которая смысла не имеет),
					 * но вдруг ПЭК так себя ведёт и в других ситуациях?
					 */
					if (0 < $deliveryTime) {
						$methodAuto =
							array_merge(
								$methodAuto
								,array(
									self::RESULT__DELIVERY_TIME_MIN => $deliveryTime
									,self::RESULT__DELIVERY_TIME_MAX => $deliveryTime + 3
								)
							)
						;
					}
					$result[Df_Pec_Model_Method_Ground::METHOD] = $methodAuto;
				}
				$this->{__METHOD__} = $result;
			}
			catch (Exception $e) {
				df_notify(
					"Получили следующий неожиданный ответ от ПЭК:\n%s"
					,rm_print_params($responseAsArray)
				);
				throw $e;
			}
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Pec_Model_Request_Rate */
	private function getApiRequest() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Pec_Model_Request_Rate::i(array(
				'deliver' =>
					array(
						'gidro' => rm_01($this->getServiceConfig()->needCargoTailLoaderAtDestination())
						,'moscow' => 0
						,'speed' => 0
						,'tent' => rm_01($this->getServiceConfig()->needRemoveAwningAtDestination())
						,'town' => $this->getLocationDestinationId()
					)
				,'take' =>
					array(
						'gidro' => rm_01($this->getServiceConfig()->needCargoTailLoaderAtOrigin())
						,'moscow' =>
							df_a(
								array(
									Df_Pec_Model_Config_Source_MoscowCargoReceptionPoint
										::OPTION_VALUE__OUTSIDE => 0
									,Df_Pec_Model_Config_Source_MoscowCargoReceptionPoint
										::OPTION_VALUE__INSIDE_LITTLE_RING_RAILWAY => 1
									,Df_Pec_Model_Config_Source_MoscowCargoReceptionPoint
										::OPTION_VALUE__INSIDE_THIRD_RING_ROAD => 2
									,Df_Pec_Model_Config_Source_MoscowCargoReceptionPoint
										::OPTION_VALUE__INSIDE_GARDEN_RING => 3
								)
								,$this->getServiceConfig()->getMoscowCargoReceptionPoint()
								,0
							)
						,'speed' => 0
						,'tent' => rm_01($this->getServiceConfig()->needRemoveAwningAtOrigin())
						,'town' => $this->getLocationSourceId()
					)
				,'fast' => 0
				,'fixedbox' => rm_01($this->getServiceConfig()->needRigidContainer())
				,'night' => rm_01($this->getServiceConfig()->needOvernightDelivery())
				,'places' => $this->getPlaces()
				,'plombir' => $this->getServiceConfig()->getSealCount()
				,'strah' => $this->getRequest()->getDeclaredValueInRoubles()
			));
		}
		return $this->{__METHOD__};
	}

	/**
	 * 15 октября 2013 года заметил, что идентификаторы населённых пунктов ПЭК
	 * перестали являться всегда целыми числами.
	 * Например, идентификаторы белорусских населённых пунктов содержать впереди челого числа тильду:
		[ГРОДНО] => ~102148
		[МИНСК] => ~102150
		[МОГИЛЕВ] => ~102152
	 * А идентификаторы некоторых российских населённых пунктов начинаются с минуса:
		[АРМАВИР] => -478
	 * @return string
	 */
	private function getLocationDestinationId() {
		df_assert($this->getRequest()->getDestinationCity(), 'Укажите город.');
		/** @var string $result */
		$result =
			df_a(
				Df_Pec_Model_Request_Locations::s()->getResponseAsArray()
				,df_h()->directory()->normalizeLocationName(
					$this->getRequest()->getDestinationCity()
				)
			)
		;
		if (is_null($result)) {
			$this->getRequest()->throwExceptionInvalidDestination();
		}
		df_result_string($result);
		return $result;
	}

	/**
	 * 15 октября 2013 года заметил, что идентификаторы населённых пунктов ПЭК
	 * перестали являться всегда целыми числами.
	 * Например, идентификаторы белорусских населённых пунктов содержать впереди челого числа тильду:
		[ГРОДНО] => ~102148
		[МИНСК] => ~102150
		[МОГИЛЕВ] => ~102152
	 * А идентификаторы некоторых российских населённых пунктов начинаются с минуса:
		[АРМАВИР] => -478
	 * @return string
	 */
	private function getLocationSourceId() {
		df_assert(
			$this->getRequest()->getDestinationCity()
			, 'Администратор должен указать город склада магазина'
		);
		/** @var string $result */
		$result =
			df_a(
				Df_Pec_Model_Request_Locations::s()->getResponseAsArray()
				,df_h()->directory()->normalizeLocationName(
					$this->getRequest()->getOriginCity()
				)
			)
		;
		if (is_null($result)) {
			df_error(
				'Доставка из населённого пункта %s невозможна'
				,$this->getRequest()->getOriginCity()
			);
		}
		df_result_string($result);
		return $result;
	}

	/** @return array(array(float|int)) */
	private function getPlaces() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(array(float|int)) $result */
			$result = array();
			foreach ($this->getRequest()->getQuoteItemsSimple() as $quoteItem) {
				/** @var Mage_Sales_Model_Quote_Item $quoteItem */
				/** @var Df_Catalog_Model_Product $product */
				$product =
					$this->getRequest()->getProductsWithDimensions()->getItemById(
						$quoteItem->getProductId()
					)
				;
				df_assert($product);
				/** @var array $productDimensionsInMeters */
				$productDimensionsInMeters =
					array_map(
						array(df()->units()->length(), 'convertToMetres')
						,array(
							$product->getLength()
							,$product->getWidth()
							,$product->getHeight()
						)
					)
				;
				df_assert_array($productDimensionsInMeters);
				/** @var array(float|int) $place */
				$place =
					array_merge(
						$productDimensionsInMeters
						,array(
									df_a($productDimensionsInMeters, 0)
								*
									df_a($productDimensionsInMeters, 1)
								*
									df_a($productDimensionsInMeters, 2)
							,df()->units()->weight()->convertToKilogrammes(
								rm_float($product->getWeight())
							)
							// Груз габаритен?
							,1
							,rm_01($this->getServiceConfig()->needRigidContainer())
						)
					)
				;
				/** @var int $qty */
				$qty = Df_Sales_Model_Quote_Item_Extended::i($quoteItem)->getQty();
				for($index = 0; $index < $qty; $index++) {
					$result[]= $place;
				}
			}
			df_result_array($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Shipping_Model_Rate_Request */
	private function getRequest() {return $this->cfg(self::P__REQUEST);}

	/** @return Df_Shipping_Model_Config_Facade */
	private function getRmConfig() {return $this->cfg(self::P__RM_CONFIG);}

	/** @return Df_Pec_Model_Config_Area_Service */
	private function getServiceConfig() {return $this->getRmConfig()->service();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__REQUEST, Df_Shipping_Model_Rate_Request::_CLASS)
			->_prop(self::P__RM_CONFIG, Df_Shipping_Model_Config_Facade::_CLASS)
		;
	}
	const _CLASS = __CLASS__;
	const P__REQUEST = 'request';
	const P__RM_CONFIG = 'rm_config';
	const RESULT__DELIVERY_TIME_MAX = 'delivery_time_max';
	const RESULT__DELIVERY_TIME_MIN = 'delivery_time_min';
	const RESULT__RATE = 'rate';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Pec_Model_Api_Calculator
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}