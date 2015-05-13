<?php
class Df_Shipping_Model_Config_Area_Service	extends Df_Shipping_Model_Config_Area_Abstract {
	/** @return bool */
	public function enableSmsNotification() {
		return $this->getVarFlag(self::KEY__VAR__ENABLE_SMS_NOTIFICATION);
	}

	/**
	 * Варинты доставки, предоставляемые данной службой доставки
	 * @return array(array(string => string))
	 */
	public function getAvailableShippingMethodsAsOptionArray() {
		return $this->getConstManager()->getAvailableShippingMethodsAsOptionArray();
	}

	/** @return string[] */
	public function getDisabledShippingMethods() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				array_diff(
					df_column(
						$this->getAvailableShippingMethodsAsOptionArray()
						,Df_Admin_Model_Config_Source::OPTION_KEY__VALUE
					)
					,$this->getSelectedShippingMethods()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string|null */
	public function getSelectedShippingMethod() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $result */
			$result = $this->getVar(self::KEY__VAR__SHIPPING_METHOD);
			if (self::KEY__VAR__SHIPPING_METHOD__NO === $result) {
				$result = null;
			}
			$this->{__METHOD__} = rm_n_set($result);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return string|null */
	public function getSelectedShippingMethodCode() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(
				df_a(
					df_a(
						$this->getConstManager()->getAvailableShippingMethodsAsCanonicalConfigArray()
						,$this->getSelectedShippingMethod()
						,array()
					)
					,'code'
				)
			);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/**
	 * Возвращает значения поля code способа оплаты.
	 * Данный метод имеет смысл, когда значения поля code — числовые
	 * @return string[]
	 */
	public function getSelectedShippingMethodCodes() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_column(
					array_intersect_key(
						$this->getConstManager()->getAvailableShippingMethodsAsCanonicalConfigArray()
						,array_flip($this->getSelectedShippingMethods())
					)
					,'code'
				)
			;
			df_result_array($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return string[] */
	public function getSelectedShippingMethods() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $resultAsString */
			$resultAsString = $this->getVar(self::KEY__VAR__SHIPPING_METHODS);
			df_assert_string($resultAsString);
			$this->{__METHOD__} =
				(Df_Admin_Model_Config_Form_Element_Multiselect::RM__ALL === $resultAsString)
				? df_column(
					$this->getAvailableShippingMethodsAsOptionArray()
					,Df_Admin_Model_Config_Source::OPTION_KEY__VALUE
				)
				: df_parse_csv($resultAsString)
			;
			df_result_array($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * Работает ли модуль в тестовом режиме?
	 * Обратите внимание, что если в настройках отсутствует ключ «test»,
	 * то модуль будет всегда находиться в рабочем режиме.
	 * @return bool
	 */
	public function isTestMode() {
		// Eсли в настройках отсутствует ключ «test»,
		// то модуль будет всегда находиться в рабочем режиме.
		return $this->getVarFlag(self::KEY__VAR__TEST);
	}

	/** @return bool */
	public function makeAccompanyingForms() {
		return $this->getVarFlag(self::KEY__VAR__MAKE_ACCOMPANYING_FORMS);
	}

	/** @return bool */
	public function needAcceptCashOnDelivery() {
		return $this->getVarFlag(self::KEY__VAR__NEED_ACCEPT_CASH_ON_DELIVERY);
	}

	/** @return bool */
	public function needDeliverCargoToTheBuyerHome() {
		return $this->getVarFlag(self::KEY__VAR__NEED_DELIVER_CARGO_TO_THE_BUYER_HOME);
	}

	/** @return bool */
	public function needGetCargoFromTheShopStore() {
		return $this->getVarFlag(self::KEY__VAR__NEED_GET_CARGO_FROM_THE_SHOP_STORE);
	}

	/** @return bool */
	public function needPacking() {return $this->getVarFlag(self::KEY__VAR__NEED_PACKING);}

	/**
	 * @override
	 * @return string
	 */
	protected function getAreaPrefix() {return self::AREA_PREFIX;}

	const _CLASS = __CLASS__;
	const AREA_PREFIX = 'service';
	const KEY__VAR__ENABLE_SMS_NOTIFICATION = 'enable_sms_notification';
	const KEY__VAR__NEED_ACCEPT_CASH_ON_DELIVERY = 'need_accept_cash_on_delivery';
	const KEY__VAR__NEED_DELIVER_CARGO_TO_THE_BUYER_HOME = 'need_deliver_cargo_to_the_buyer_home';
	const KEY__VAR__NEED_GET_CARGO_FROM_THE_SHOP_STORE = 'need_get_cargo_from_the_shop_store';
	const KEY__VAR__MAKE_ACCOMPANYING_FORMS = 'make_accompanying_forms';
	const KEY__VAR__NEED_PACKING = 'need_packing';
	const KEY__VAR__SHIPPING_METHOD = 'shipping_method';
	const KEY__VAR__SHIPPING_METHOD__NO = 'no';
	const KEY__VAR__SHIPPING_METHODS = 'shipping_methods';
	const KEY__VAR__TEST = 'test';
}