<?php
class Df_Core_Model_Cache_Module extends Df_Core_Model {
	/**
	 * @param string $moduleName
	 * @return bool
	 */
	public function isEnabled($moduleName) {
		if (!isset($this->_cache[$moduleName])) {
			/** @var Varien_Simplexml_Element|null $moduleConfig */
			$moduleConfig = Mage::app()->getConfig()->getModuleConfig($moduleName);
			$this->_cache[$moduleName] = $moduleConfig && df_leaf_b($moduleConfig->{'active'});
			$this->markCachedPropertyAsModified(self::$F__CACHE);
		}
		return $this->_cache[$moduleName];
	}
	/** @var array(string => bool) */
	protected $_cache;

	/**
	 * @used-by Df_Core_Model::cacheSaveProperty()
	 * @override
	 * @return string[]
	 */
	protected function cacheTags() {return array(Mage_Core_Model_Config::CACHE_TAG);}

	/**
	 * @override
	 * @return string
	 */
	protected function cacheType() {return 'config';}

	/**
	 * @override
	 * @see Df_Core_Model::cached()
	 * @return string[]
	 */
	protected function cached() {return array(self::$F__CACHE);}

	/** @var string */
	private static $F__CACHE = '_cache';
	/** @return Df_Core_Model_Cache_Module */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}