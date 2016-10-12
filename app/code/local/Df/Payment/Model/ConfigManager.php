<?php
abstract class Df_Payment_Model_ConfigManager extends Df_Core_Model {
	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getKeyBase();

	/** @return Df_Payment_Model_Method_Base */
	public function getPaymentMethod() {
		return $this->cfg(self::P__PAYMENT_METHOD);
	}

	/** @return Mage_Core_Model_Store */
	public function getStore() {
		return $this->cfg(self::P__STORE);
	}

	/** @return array(string => string) */
	protected function getPostProcessTemplates() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array_merge(
				array('{телефон магазина}' => df_cfg()->base()->getStorePhone($this->getStore()))
				,$this->getPaymentMethod()->getConfigTemplates()
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param mixed $value
	 * @return mixed
	 */
	protected function postProcessValue($value) {
		/** @var mixed $result */
		$result = $value;
		/** @var bool doPostProcessing */
		static $doPostProcessing = false;
		if (
				(true !== $doPostProcessing)
			&&
				is_string($value)
			&&
				(false !== strpos($result, '{'))
		) {
			// чтобы не попадать в рекурсию
			$doPostProcessing = true;
			$result = strtr($result, $this->getPostProcessTemplates());
			$doPostProcessing = false;
		}
		return $result;
	}

	/**
	 * @param string $key
	 * @return string
	 */
	protected function preprocessKey($key) {
		df_param_string($key, 0);
		return rm_config_key($this->getKeyBase(), $this->getPaymentMethod()->getRmId(), $key);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__PAYMENT_METHOD, Df_Payment_Model_Method_Base::_CLASS)
			->_prop(self::P__STORE, 'Mage_Core_Model_Store');
		;
	}
	const _CLASS = __CLASS__;
	const P__PAYMENT_METHOD = 'payment_method';
	const P__STORE = 'store';
}