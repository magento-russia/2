<?php
class Df_Core_Model_Settings_Group extends Df_Core_Model_Abstract {
	/**
	 * @param string $configKeySuffix
	 * @param array|string $prefixes[optional]
	 * @param mixed $defaultValue[optional]
	 * @param int|string|Mage_Core_Model_Store $store[optional]
	 * @return mixed
	 */
	public function getValue(
		$configKeySuffix
		,$prefixes = array()
		,$defaultValue = null
		,$store = null
	) {
		df_param_string($configKeySuffix, 0);
		/**
		 * country_id => country
		 * region_id => region
		 */
		/**
		 * Оптимизация выражения
		 * $configKeySuffix = preg_replace('#_id$#', '', $configKeySuffix);
		 */
		if ('_id' === substr($configKeySuffix, -3)) {
			$configKeySuffix = substr($configKeySuffix, 0, -3);
		}
		if (!is_string($prefixes)) {
			df_param_array($prefixes, 1);
		}
		/** @var mixed $result */
		$result =
			Mage::getStoreConfig(
				$this->expandConfigKey($this->implodePrefixes($configKeySuffix, $prefixes))
				,$store
			)
		;
		if (is_null($result)) {
			$result = $defaultValue;
		}
		return $result;
	}

	/**
	 * @param array|string $prefixes
	 * @return Df_Core_Model_Settings_Group
	 */
	protected function appendPrefixes($prefixes) {
		if (!is_string($prefixes)) {
			df_param_array($prefixes, 1);
		}

		$this
			->setData(
				self::P__PREFIXES
				,array_merge(
					$this->getPrefixesAsArray()
					,is_array($prefixes) ? $prefixes : array($prefixes)
				)
			)
		;
		return $this;
	}

	/** @return string */
	protected function getGroup() {return $this->cfg(self::P__GROUP);}

	/** @return string */
	protected function getSection() {return $this->cfg(self::P__SECTION);}

	/**
	 * @param string $configKeySuffix
	 * @param array|string $prefixes[optional]
	 * @param bool $defaultValue[optional]
	 * @param int|string|Mage_Core_Model_Store $store[optional]
	 * @return bool
	 */
	protected function getYesNo(
		$configKeySuffix
		,$prefixes = array()
		,$defaultValue = null
		,$store = null
	) {
		df_param_string($configKeySuffix, 0);
		if (!is_string($prefixes)) {
			df_param_array($prefixes, 1);
		}
		/** @var bool $result */
		$result =
			rm_bool(
				$this->getValue(
					$configKeySuffix
					,$prefixes
					,$defaultValue
					,$store
				)
			)
		;
		if (is_null($result)) {
			$result = $defaultValue;
		}
		df_result_boolean($result);
		return $result;
	}

	/**
	 * @param array|string $prefixes
	 * @return Df_Core_Model_Settings_Group
	 */
	protected function prependPrefixes($prefixes) {
		if (!is_string($prefixes)) {
			df_param_array($prefixes, 1);
		}

		$this
			->setData(
				self::P__PREFIXES
				,array_merge(
					is_array($prefixes) ? $prefixes : array($prefixes)
					,$this->getPrefixesAsArray()
				)
			)
		;
		return $this;
	}

	/**
	 * @param string $configKeySuffix
	 * @param array|string $prefixes
	 * @return string
	 */
	private function implodePrefixes($configKeySuffix, $prefixes) {
		df_param_string($configKeySuffix, 0);
		if (!is_string($prefixes)) {
			df_param_array($prefixes, 1);
		}
		if (!is_array($prefixes)) {
			$prefixes = array($prefixes);
		}
		return implode(self::PREFIX_SEPARATOR, array_merge($prefixes, array($configKeySuffix)));
	}

	/**
	 * @param string $configKeySuffix
	 * @return string
	 */
	private function expandConfigKey($configKeySuffix) {
		df_param_string($configKeySuffix, 0);
		return rm_config_key(
			$this->getSection()
			,$this->getGroup()
			,$this->implodePrefixes($configKeySuffix, $this->getPrefixesAsArray())
		);
	}

	/** @return array */
	private function getPrefixesAsArray() {
		/** @var array $result */
		$result = $this->cfg(self::P__PREFIXES, array());
		if (is_string($result)) {
			$result = array($result);
		}
		df_result_array($result);
		return $result;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__GROUP, self::V_STRING_NE)
			->_prop(self::P__SECTION, self::V_STRING_NE)
			->_prop(self::P__PREFIXES, self::V_ARRAY, false)
		;
	}
	const _CLASS = __CLASS__;
	const P__GROUP = 'group';
	const P__PREFIXES = 'prefixes';
	const P__SECTION = 'section';
	const PREFIX_SEPARATOR = '__';

}