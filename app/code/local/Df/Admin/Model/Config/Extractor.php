<?php
abstract class Df_Admin_Model_Config_Extractor extends Df_Core_Model_Abstract {
	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getEntityName();

	/**
	 * @param string $fieldNameUniqueSuffix
	 * @return bool
	 */
	protected function getYesNo($fieldNameUniqueSuffix) {
		return rm_bool($this->getValue($fieldNameUniqueSuffix));
	}

	/**
	 * @param string $fieldNameUniqueSuffix
	 * @param string $defaultValue[optional]
	 * @return string
	 */
	protected function getValue($fieldNameUniqueSuffix, $defaultValue = '') {
		df_param_string($fieldNameUniqueSuffix, 0);
		/** @var string $result */
		$result =
			Mage::getStoreConfig(
				rm_config_key(
					$this->getConfigGroupPath()
					,$this->implode(
						$this->getConfigKeyPrefix()
						,$this->getEntityName()
						,$fieldNameUniqueSuffix
					)
				)
				,$this->getStore()
			)
		;
		if (is_null($result)) {
			$result = $defaultValue;
		}
		df_result_string($result);
		return $result;
	}

	/**
	 * @param string $fieldNameUniqueSuffix
	 * @return string
	 */
	private function composePath($fieldNameUniqueSuffix) {
		df_param_string($fieldNameUniqueSuffix, 0);
		return rm_config_key(
			$this->getConfigGroupPath()
			,$this->implode(
				$this->getConfigKeyPrefix()
				,$this->getEntityName()
				,$fieldNameUniqueSuffix
			)
		);
	}

	/** @return string */
	private function getConfigGroupPath() {return $this->cfg(self::P__CONFIG_GROUP_PATH);}

	/** @return string */
	private function getConfigKeyPrefix() {return $this->cfg(self::P__CONFIG_KEY_PREFIX);}
	
	/** @return Mage_Core_Model_Store */
	private function getStore() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Mage::app()->getStore($this->cfg(self::P__STORE));
			df_assert($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function implode() {
		/** @var string[] $elements */
		$elements = func_get_args();
		return rm_concat_clean('__', $elements);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__CONFIG_KEY_PREFIX, self::V_STRING)
			->_prop(self::P__CONFIG_GROUP_PATH, self::V_STRING_NE)
			->_prop(self::P__STORE,	Df_Core_Const::STORE_CLASS,	false)
		;
	}
	const _CLASS = __CLASS__;
	const P__CONFIG_GROUP_PATH = 'config_group_path';
	const P__CONFIG_KEY_PREFIX = 'config_key_prefix';
	const P__STORE = 'store';
}