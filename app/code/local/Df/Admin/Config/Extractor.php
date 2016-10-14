<?php
abstract class Df_Admin_Config_Extractor extends Df_Core_Model {
	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getEntityName();

	/**
	 * @param string $suffix
	 * @return bool
	 */
	protected function getYesNo($suffix) {return rm_bool($this->getValue($suffix));}

	/**
	 * @param string $suffix
	 * @param string $defaultValue [optional]
	 * @return string
	 */
	protected function getValue($suffix, $defaultValue = '') {
		/** @var string $result */
		$result = $this->store()->getConfig($this->getFullPrefix() . $suffix);
		return is_null($result) ? $defaultValue : $result;
	}

	/** @return string */
	private function getFullPrefix() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_concat_xpath(
				$this->getGroupPath(), df_ccc('__', $this->getKeyPrefix(), $this->getEntityName())
			) . '__';
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getGroupPath() {return $this->cfg(self::$P__GROUP_PATH);}

	/** @return string */
	private function getKeyPrefix() {return $this->cfg(self::$P__KEY_PREFIX);}
	
	/** @return Df_Core_Model_StoreM */
	private function store() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_store($this->cfg(self::$P__STORE));
			df_assert($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__KEY_PREFIX, RM_V_STRING)
			->_prop(self::$P__GROUP_PATH, RM_V_STRING_NE)
			->_prop(self::$P__STORE,	Df_Core_Model_StoreM::_C, false)
		;
	}
	/** @var string */
	private static $P__GROUP_PATH = 'group_path';
	/** @var string */
	private static $P__KEY_PREFIX = 'key_prefix';
	/** @var string */
	private static $P__STORE = 'store';

	/**
	 * @used-by Df_Admin_Config_Font::i()
	 * @param string $class
	 * @param string $groupPath
	 * @param string $keyPrefix [optional]
	 * @param Df_Core_Model_StoreM|int|string|bool|null [optional]
	 * @return Df_Admin_Config_Extractor
	 */
	protected static function ic($class, $groupPath, $keyPrefix = '', $store = null) {
		return rm_ic($class, __CLASS__, array(
			self::$P__GROUP_PATH => $groupPath, self::$P__KEY_PREFIX => $keyPrefix, self::$P__STORE => $store
		));
	}
}