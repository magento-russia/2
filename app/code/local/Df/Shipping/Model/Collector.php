<?php
class Df_Shipping_Model_Collector extends Df_Core_Model_Abstract {
	/** @return Mage_Shipping_Model_Rate_Result */
	public function getRateResult() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(Df_Shipping_Model_Method|Df_Shipping_Model_Rate_Result_Error) $resultAsArray */
			$resultAsArray = array();
			/**
			 * Обратите внимание на два вложенных друг в друга блока try...catch.
			 * Одного было бы недостаточно,
			 * потому что исключительная ситуация может возникнуть
			 * при вызове $this->getMethods()
			 */
			try {
				if (
						!$this->getMethods()
					&&
						$this->getRmConfig()->frontend()->needDisplayDiagnosticMessages()
				) {
					df_error(
						strtr(
							'К сожалению, в силу особенностей Вашего заказа, система не в состоянии '
							.'в автоматическом режиме подобрать для него подходящий режим доставки '
							.'службой {carrier}. '
							.'Если Вы хотите использовать именно службу {carrier} — пожалуйста, '
							.'оформите Ваш заказ по телефону.'
							,array(
								'{carrier}' => $this->getCarrier()->getTitle()
							)
						)
					);
				}
				else {
					foreach ($this->getMethods() as $method) {
						/** @var Df_Shipping_Model_Method $method */
						// При расчёте стоимость доставки может произойти исключительная ситуация.
						// Чтобы исключительная ситуация не происходила в ядре Magento,
						// мы производит расчёт стоимости прямо сейчас.
						try {
							if ($method->isApplicable()) {
								$method->getPrice();
								$resultAsArray[]= $method;
							}
						}
						catch(Exception $e) {
							$resultAsArray[]= $this->createRateResultError($e, $method);
						}
					}
				}
			}
			catch(Exception $e) {
				$resultAsArray[]= $this->createRateResultError($e);
			}
			/** @var $resultAsArrayNonError */
			$resultAsArrayNonError = array();
			foreach ($resultAsArray as $resultItem) {
				if (!($resultItem instanceof Df_Shipping_Model_Rate_Result_Error)) {
					$resultAsArrayNonError[]= $resultItem;
				}
			}
			/**
			 * Если опция показа диагностических сообщений выключена,
			 * то оставлять такие сообщения в результирующем массиве нельзя,
			 * потому что иначе система не покажет при оформлении заказа доступные тарифы:
			 * @see Mage_Shipping_Model_Shipping::collectCarrierRates
			 * [code]
					if ($carrier->getConfigData('showmethod') == 0 && $result->getError()) {
					   return $this;
				    }
			 * [/code]
			 *
			 */
			if (
					$resultAsArrayNonError
				&&
					!$this->getCarrier()->getRmConfig()->frontend()->needDisplayDiagnosticMessages()
			) {
				$resultAsArray = $resultAsArrayNonError;
			}
			/**
			 * Раньше тут стоял комментарий:
			 * «Если все тарифы доставки оказались сбойными,
			 * то убираем все сбойные сообщения, кроме первого».
			 * Зачем это нужно было — не помню.
			 */
//			if (0 === count($resultAsArrayNonError)) {
//				$resultAsArray = array(rm_first($resultAsArray));
//			}
			/** @var Mage_Shipping_Model_Rate_Result $result */
			$result = df_model('shipping/rate_result');
			$resultAsArray = $this->postProcessMethods($resultAsArray);
			foreach ($resultAsArray as $resultItem) {
				$result->append($resultItem);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @return void
	 * @throws Df_Shipping_Exception
	 */
	protected function checkCityDest() {
		if (!$this->cityDest()) {
			$this->error('Укажите город.');
		}
	}

	/** @return string|null */
	protected function cityDest() {return $this->rr()->getDestinationCity();}

	/**
	 * @used-by \Df_RussianPost_Model_Collector::getMethods()
	 * @param string|object $c
	 * @param string|null $title [optional]
	 * @return Df_Shipping_Model_Method
	 */
	protected function createMethod($c, $title = null) {
		$result = df_model($c); /** @var Df_Shipping_Model_Method $result */
		df_assert($result instanceof Df_Shipping_Model_Method);
		$result
			->setRequest($this->getRateRequest())
			->setCarrier($this->getCarrier()->getCarrierCode())
			/**
			 * При оформлении заказа Magento игнорирует данное значение
			 * и берёт заголовок способа доставки из реестра настроек:
			 *
			 *	public function getCarrierName($carrierCode)
			 *	{
			 *		if ($name = Mage::getStoreConfig('carriers/'.$carrierCode.'/title')) {
			 *			return $name;
			 *		}
			 *		return $carrierCode;
			 *	}
			 */
			->setCarrierTitle($this->getCarrier()->getTitle())
			->addData(array(Df_Shipping_Model_Method::P__CARRIER_INSTANCE => $this->getCarrier()))
		;
		if ($title) {
			$result[Df_Shipping_Model_Method::P__METHOD_TITLE] = "$title:";
		}
		return $result;
	}

	/** @return Df_Shipping_Model_Carrier */
	protected function getCarrier() {return $this->cfg(self::P__CARRIER);}

	/**
	 * @used-by Df_Shipping_Model_Collector_Simple::fees()
	 * @return float
	 */
	protected function declaredValueBase() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->getRateRequest()->getPackageValue()
				* 0.01
				* $this->getRmConfig()->admin()->getDeclaredValuePercent()
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @return void
	 * @throws Df_Shipping_Exception
	 */
	protected function error() {
		/** @var mixed $args */
		$args = func_get_args();
		throw new Df_Shipping_Exception(call_user_func_array('sprintf', $args));
	}

	/** @return Df_Shipping_Model_Method[] */
	protected function getMethods() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Shipping_Model_Method[] $result */
			$result = array();
			foreach ($this->getCarrier()->getAllowedMethodsAsArray() as $methodData) {
				/** @var array(string => string) $methodData */
				df_assert_array($methodData);
				/** @var string $class */
				$class = df_a($methodData, 'class');
				df_assert_string_not_empty($class);
				/** @var string $title */
				$title = df_a($methodData, 'title');
				if (!$title) {
					df_error('Безымянный способ доставки: %s.', $class);
				}
				$result[]= $this->createMethod($class, $title);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Shipping_Model_Rate_Request */
	protected function getRateRequest() {return $this->cfg(self::P__RATE_REQUEST);}

	/**
	 * @param int|string|null|Mage_Core_Model_Store $storeId[optional]
	 * @return Df_Shipping_Model_Config_Facade
	 */
	protected function getRmConfig($storeId = null) {
		return $this->getCarrier()->getRmConfig($storeId);
	}

	/**
	 * @param Df_Shipping_Model_Method|Df_Shipping_Model_Rate_Result_Error[] $methods
	 * @return Df_Shipping_Model_Method|Df_Shipping_Model_Rate_Result_Error[]
	 */
	protected function postProcessMethods(array $methods) {
		return $methods;
	}

	/** @return Df_Shipping_Model_Rate_Request */
	protected function rr() {return $this->getRateRequest();}

	/**
	 * @param Exception $e
	 * @param Df_Shipping_Model_Method $method|null [optional]
	 * @return Df_Shipping_Model_Rate_Result_Error
	 */
	private function createRateResultError(Exception $e, $method = null) {
		/** @var string $message */
		$message = rm_ets($e);
		/**
		 * Раньше тут стояла проверка ($e instanceof Df_Core_Exception_Internal),
		 * однако она была не совсем верной, потому что не относила к системным
		 * исключительные ситуации из ядра Magento и Zend Framework
		 */
		if (!($e instanceof Df_Core_Exception_Client)) {
			df_notify_exception($e);
			/** @var Df_Shipping_Model_Method|Df_Shipping_Model_Carrier $evaluator */
			$evaluator = is_null($method) ? $this->getCarrier() : $method;
			$message =
				df_no_escape(
					$evaluator->evaluateMessage(
						df_cfg()->shipping()->message()->getFailureGeneral(
							$this->getRateRequest()->getStoreId()
						)
					)
				)
			;
		}
		return Df_Shipping_Model_Rate_Result_Error::i($this->getCarrier(), $message);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__CARRIER, Df_Shipping_Model_Carrier::_CLASS)
			->_prop(self::P__RATE_REQUEST, Df_Shipping_Model_Rate_Request::_CLASS)
		;
	}
	const _CLASS = __CLASS__;
	const P__CARRIER = 'carrier';
	const P__RATE_REQUEST = 'rate_request';
}