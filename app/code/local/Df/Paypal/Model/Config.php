<?php
class Df_Paypal_Model_Config extends Mage_Paypal_Model_Config {
	/**
	 * 2015-08-08
	 * Цели перекрытия:
	 * 1) Поддержка России в качестве страны продавца.
	 * 2) Поддержка российского рубля в качестве валюты платежа.
	 * @override
	 * @see Mage_Paypal_Model_Config::__construct()
	 * @param mixed[] $params
	 */
	public function __construct($params = array()) {
		$this->_supportedCountryCodes[]= 'RU';
		$this->_supportedCurrencyCodes[]= 'RUB';
		parent::__construct($params);
	}
}