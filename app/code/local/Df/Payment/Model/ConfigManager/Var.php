<?php
class Df_Payment_Model_ConfigManager_Var extends Df_Payment_Model_ConfigManager {
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
		$result = $this->postProcessValue($result);
		return $result;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getKeyBase() {
		return self::KEY__BASE;
	}

	/**
	 * @param string $key
	 * @return string
	 */
	protected function preprocessKey($key) {
		df_param_string($key, 0);
		return rm_config_key(self::KEY__BASE, $this->getPaymentMethod()->getRmId(), $key);
	}
	const _CLASS = __CLASS__;
	const KEY__BASE = 'df_payment';
	/**
	 * @param Df_Payment_Model_Method_Base $paymentMethod
	 * @param Mage_Core_Model_Store $store
	 * @return Df_Payment_Model_ConfigManager_Var
	 */
	public static function i(Df_Payment_Model_Method_Base $paymentMethod, Mage_Core_Model_Store $store) {
		return new self(array(self::P__PAYMENT_METHOD => $paymentMethod, self::P__STORE => $store));
	}
}