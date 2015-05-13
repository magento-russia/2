<?php
class Df_Shipping_Model_ConfigManager_Var extends Df_Shipping_Model_ConfigManager {
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyBase() {
		return self::KEY__BASE;
	}

	/**
	 * @param string $key
	 * @param mixed $defaultValue[optional]
	 * @return mixed
	 */
	public function getValue($key, $defaultValue = null) {
		df_param_string($key, 0);
		/** @var string $result */
		$result = $this->getStore()->getConfig($this->preprocessKey($key));
		if (is_null($result)) {
			$result = $defaultValue;
		}
		return $result;
	}

	/**
	 * @param string $key
	 * @param mixed $defaultValue[optional]
	 * @return mixed
	 */
	public function getValueLegacy($key, $defaultValue = null) {
		df_param_string($key, 0);
		/** @var string $result */
		$result = $this->getStore()->getConfig($this->preprocessKeyLegacy($key));
		if (is_null($result)) {
			$result = $defaultValue;
		}
		return $result;
	}

	/**
	 * @param string $key
	 * @return string
	 */
	protected function preprocessKeyLegacy($key) {
		df_param_string($key, 0);
		return rm_config_key(self::KEY__BASE_LEGACY, $this->getCarrier()->getCarrierCode(), $key);
	}
	const _CLASS = __CLASS__;
	const KEY__BASE = 'df_shipping';
	const KEY__BASE_LEGACY = 'carriers';
	/**
	 * @static
	 * @param Df_Shipping_Model_Carrier $carrier
	 * @param Mage_Core_Model_Store $store
	 * @return Df_Shipping_Model_ConfigManager_Var
	 */
	public static function i(Df_Shipping_Model_Carrier $carrier, Mage_Core_Model_Store $store) {
		return new self(array(self::P__CARRIER => $carrier, self::P__STORE => $store));
	}
}