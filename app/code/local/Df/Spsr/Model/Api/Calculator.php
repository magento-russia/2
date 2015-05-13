<?php
class Df_Spsr_Model_Api_Calculator extends Df_Core_Model_Abstract {
	/** @return string */
	public function getErrorMessage() {
		/** @var string $result */
		$result = $this->getRateRequest()->getErrorMessage();
		if ('Город получателя не найден' === $result) {
			$result =
				rm_sprintf(
					'К сожалению, служба СПСР не отправляет грузы в населённый пункт «%s».'
					,$this->getRequest()->getDestinationCity()
				)
			;
		}
		df_result_string($result);
		return $result;
	}

	/** @return array(array(string => string|int)) */
	public function getRates() {
		return $this->getRateRequest()->getRates();
	}

	/** @return float */
	private function getDeclaredValue() {
		return $this->cfg(self::P__DECLARED_VALUE);
	}

	/**
	 * Обратите внимание, что результатом является не число,
	 * а строка вида: «63249745|3»
	 * @return string
	 */
	private function getLocationDestinationId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getRequest()->getLocatorDestination()->getResult();
		}
		return $this->{__METHOD__};
	}

	/**
	 * Обратите внимание, что результатом является не число,
	 * а строка вида: «63249745|3»
	 * @return string
	 */
	private function getLocationOriginId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getRequest()->getLocatorOrigin()->getResult();
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Spsr_Model_Request_Rate */
	private function getRateRequest() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Spsr_Model_Request_Rate::i(array(
				Df_Spsr_Model_Request_Rate::POST__DECLARED_VALUE => $this->getDeclaredValue()
				,Df_Spsr_Model_Request_Rate::POST__LOCATION__SOURCE => $this->getLocationOriginId()
				,Df_Spsr_Model_Request_Rate::POST__LOCATION__DESTINATION =>
					$this->getLocationDestinationId()
				,Df_Spsr_Model_Request_Rate::POST__ENDORSE_DELIVERY_TIME =>
					rm_nat0($this->getServiceConfig()->endorseDeliveryTime())
				,Df_Spsr_Model_Request_Rate::POST__NOTIFY_RECIPIENT_BY_SMS =>
					rm_01($this->getServiceConfig()->enableSmsNotification())
				,Df_Spsr_Model_Request_Rate::POST__NOTIFY_SENDER_BY_SMS =>
					rm_01($this->getServiceConfig()->enableSmsNotification())
				// 0 - страхование объявления
				// 1 - тариф за объявленную стоимость
				,Df_Spsr_Model_Request_Rate::POST__INSURANCE_TYPE =>
					rm_01(
							Df_Spsr_Model_Config_Source_Insurer::OPTION_VALUE__CARRIER
						===
							$this->getServiceConfig()->getInsurer()
					)
				,Df_Spsr_Model_Request_Rate::POST__WEIGHT => $this->getRequest()->getWeightInKilogrammes()
			));
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Shipping_Model_Rate_Request */
	private function getRequest() {return $this->cfg(self::P__REQUEST);}

	/** @return Df_Shipping_Model_Config_Facade */
	private function getRmConfig() {return $this->cfg(self::P__RM_CONFIG);}

	/** @return Df_Spsr_Model_Config_Area_Service */
	private function getServiceConfig() {return $this->getRmConfig()->service();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__DECLARED_VALUE, self::V_FLOAT)
			->_prop(self::P__REQUEST, Df_Shipping_Model_Rate_Request::_CLASS)
			->_prop(self::P__RM_CONFIG, Df_Shipping_Model_Config_Facade::_CLASS)
		;
	}
	const _CLASS = __CLASS__;
	const P__DECLARED_VALUE = 'declared_value';
	const P__REQUEST = 'request';
	const P__RM_CONFIG = 'rm_config';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Spsr_Model_Api_Calculator
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}