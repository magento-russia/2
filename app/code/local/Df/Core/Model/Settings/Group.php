<?php
class Df_Core_Model_Settings_Group extends Df_Core_Model {
	/**
	 * @param string $configKeySuffix
	 * @param array|string $prefixes [optional]
	 * @param mixed $defaultValue [optional]
	 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
	 * @return mixed
	 */
	public function getValue($configKeySuffix, $prefixes = array(), $defaultValue = null, $store = null) {
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
		/** @var string|null $result */
		$result = df_store($store)->getConfig($this->expandConfigKey($this->implodePrefixes(
			$configKeySuffix, $prefixes
		)));
		return is_null($result) ? $defaultValue : $result;
	}

	/** @return string */
	protected function getGroup() {return $this->cfg(self::P__GROUP);}

	/** @return string */
	protected function getSection() {return $this->cfg(self::P__SECTION);}

	/**
	 * @param string $configKeySuffix
	 * @param array|string $prefixes [optional]
	 * @param bool $defaultValue [optional]
	 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
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
	 * @param string $configKeySuffix
	 * @param string|string[] $prefixes
	 * @return string
	 */
	private function implodePrefixes($configKeySuffix, $prefixes) {
		df_param_string($configKeySuffix, 0);
		return implode(self::PREFIX_SEPARATOR, array_merge(df_array($prefixes), array($configKeySuffix)));
	}

	/**
	 * @param string $configKeySuffix
	 * @return string
	 */
	private function expandConfigKey($configKeySuffix) {
		df_param_string($configKeySuffix, 0);
		return df_cc_path(
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
			->_prop(self::P__GROUP, DF_V_STRING_NE)
			->_prop(self::P__SECTION, DF_V_STRING_NE)
			->_prop(self::P__PREFIXES, DF_V_ARRAY, false)
		;
	}
	const _C = __CLASS__;
	const P__GROUP = 'group';
	const P__PREFIXES = 'prefixes';
	const P__SECTION = 'section';
	const PREFIX_SEPARATOR = '__';

}