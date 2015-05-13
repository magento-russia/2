<?php
class Df_Shipping_Model_ConfigManager_Const extends Df_Shipping_Model_ConfigManager {
	/** @return array(array(string => string)) */
	public function getAvailableShippingMethodsAsCanonicalConfigArray() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Core_Model_Config_Element|null $node */
			$node = $this->getNode(self::KEY__SHIPPING_METHODS, $canBeTest = false);
			/** @var array(array(string => string)) $result */
			$result = is_null($node) ? array() : $node->asCanonicalArray();
			/**
			 * @see Varien_Simplexml_Element::asCanonicalArray может возвращать строку в случае,
			 * когда структура исходных данных не соответствует массиву.
			 */
			df_result_array($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Способы оплаты, предоставляемые данной платёжной системой
	 * @return array(array(string => string))
	 */
	public function getAvailableShippingMethodsAsOptionArray() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(array(string => string)) $result */
			$result = array();
			foreach ($this->getAvailableShippingMethodsAsCanonicalConfigArray()
				as $methodCode => $methodOptions) {
				/** @var string $methodCode */
				/** @var array $methodOptions */
				df_assert_string($methodCode);
				df_assert_array($methodOptions);
				/** @var string $methodTitle */
				$methodTitle = df_a($methodOptions, self::KEY__TITLE);
				df_assert_string($methodTitle);
				$result[]=
					array(
						Df_Admin_Model_Config_Source::OPTION_KEY__LABEL => $methodTitle
						,Df_Admin_Model_Config_Source::OPTION_KEY__VALUE => $methodCode
					)
				;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $key
	 * @param bool $canBeTest[optional]
	 * @return Mage_Core_Model_Config_Element|null
	 */
	public function getNode($key, $canBeTest = true) {
		df_param_string($key, 0);
		df_param_boolean($canBeTest, 1);
		if ($canBeTest) {
			$key =
				rm_config_key(
					$this->getCarrier()->isTestMode() ? self::KEY__TEST : self::KEY__PRODUCTION
					,$key
				)
			;
		}
		if (!isset($this->{__METHOD__}[$key][$canBeTest])) {
			/** @var Mage_Core_Model_Config_Element|null $result */
			$result = df()->config()->getNodeByKey($this->preprocessKey($key));
			if (is_null($result)) {
				// Пробуем получить стандартное значение параметра из настроек модуля Df_Shipping
				$result = df()->config()->getNodeByKey($this->preprocessKeyDefault($key));
			}
			if (!is_null($result)) {
				df_assert($result instanceof Mage_Core_Model_Config_Element);
			}
			$this->{__METHOD__}[$key][$canBeTest] = $result;
		}
		return $this->{__METHOD__}[$key][$canBeTest];
	}

	/**
	 * @param string $key
	 * @param bool $canBeTest[optional]
	 * @param string $defaultValue[optional]
	 * @return string
	 */
	public function getUrl($key, $canBeTest = true, $defaultValue = '') {
		df_param_string($key, 0);
		df_param_boolean($canBeTest, 1);
		df_param_string($defaultValue, 2);
		/** @var string $result */
		$result = $this->getValue(rm_config_key(self::KEY__URL, $key), $canBeTest);
		df_result_string($result);
		return $result;
	}

	/**
	 * @param string $key
	 * @param bool $canBeTest
	 * @param string $defaultValue[optional]
	 * @return string
	 */
	public function getValue($key, $canBeTest, $defaultValue = '') {
		df_param_string($key, 0);
		df_param_boolean($canBeTest, 1);
		df_param_string($defaultValue, 2);
		/** @var string $result */
		$result = df()->config()->getNodeValueAsString($this->getNode($key, $canBeTest));
		if ('' === $result) {
			if ($canBeTest) {
				// Пробуем получить значение без приставок test/production
				$result = df()->config()->getNodeValueAsString($this->getNode($key, !$canBeTest));
			}
		}
		if ('' === $result) {
			$result = $defaultValue;
		}
		df_result_string($result);
		return $result;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getKeyBase() {return self::KEY__BASE;}

	/**
	 * @param string $key
	 * @return string
	 */
	protected function preprocessKeyDefault($key) {
		df_param_string($key, 0);
		return rm_config_key(self::KEY__BASE, self::KEY__DEFAULT, $key);
	}

	const _CLASS = __CLASS__;
	const KEY__ALLOWED = 'allowed';
	const KEY__BASE = 'df/shipping';
	const KEY__DEFAULT = 'default';
	const KEY__PRODUCTION = 'production';
	const KEY__SHIPPING_METHODS = 'shipping-methods';
	const KEY__TEST = 'test';
	const KEY__TITLE = 'title';
	const KEY__URL = 'url';
	/**
	 * @static
	 * @param Df_Shipping_Model_Carrier $carrier
	 * @param Mage_Core_Model_Store $store
	 * @return Df_Shipping_Model_ConfigManager_Const
	 */
	public static function i(Df_Shipping_Model_Carrier $carrier, Mage_Core_Model_Store $store) {
		return new self(array(self::P__CARRIER => $carrier, self::P__STORE => $store));
	}
}