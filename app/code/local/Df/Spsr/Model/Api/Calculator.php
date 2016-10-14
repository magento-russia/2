<?php
class Df_Spsr_Model_Api_Calculator extends Df_Core_Model {
	/** @return string */
	public function getErrorMessage() {
		/** @var string $result */
		$result = $this->getRateRequest()->getErrorMessage();
		if ('Город получателя не найден' === $result) {
			$result = sprintf(
				'К сожалению, служба СПСР не отправляет грузы в населённый пункт «%s».'
				, $this->rr()->getDestinationCity()
			);
		}
		df_result_string($result);
		return $result;
	}

	/** @return array(array(string => string|int)) */
	public function getRates() {return $this->getRateRequest()->getRates();}

	/** @return Df_Checkout_Module_Config_Facade */
	private function config() {return $this->cfg(self::P__RM_CONFIG);}

	/** @return Df_Spsr_Model_Config_Area_Service */
	private function configS() {return $this->config()->service();}

	/** @return float */
	private function getDeclaredValue() {return $this->cfg(self::P__DECLARED_VALUE);}

	/**
	 * Обратите внимание, что результатом является не число,
	 * а строка вида: «63249745|3»
	 * @return string
	 */
	private function getLocationDestinationId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->rr()->getLocatorDestination()->getResult();
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
			$this->{__METHOD__} = $this->rr()->getLocatorOrigin()->getResult();
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
					rm_nat0($this->configS()->endorseDeliveryTime())
				,Df_Spsr_Model_Request_Rate::POST__NOTIFY_RECIPIENT_BY_SMS =>
					rm_01($this->configS()->enableSmsNotification())
				,Df_Spsr_Model_Request_Rate::POST__NOTIFY_SENDER_BY_SMS =>
					rm_01($this->configS()->enableSmsNotification())
				// 0 - страхование объявления
				// 1 - тариф за объявленную стоимость
				,Df_Spsr_Model_Request_Rate::POST__INSURANCE_TYPE =>
					rm_01(
							Df_Spsr_Model_Config_Source_Insurer::OPTION_VALUE__CARRIER
						===
							$this->configS()->getInsurer()
					)
				,Df_Spsr_Model_Request_Rate::POST__WEIGHT => $this->rr()->getWeightInKilogrammes()
			));
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Shipping_Rate_Request */
	private function rr() {return $this->cfg(self::P__REQUEST);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__DECLARED_VALUE, RM_V_FLOAT)
			->_prop(self::P__REQUEST, Df_Shipping_Rate_Request::_C)
			->_prop(self::P__RM_CONFIG, Df_Checkout_Module_Config_Facade::_C)
		;
	}
	const _C = __CLASS__;
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