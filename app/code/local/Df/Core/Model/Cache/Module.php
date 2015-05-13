<?php
class Df_Core_Model_Cache_Module extends Df_Core_Model_DestructableSingleton {
	/**
	 * @param string $moduleName
	 * @return bool
	 */
	public function isEnabled($moduleName) {
		if (!isset($this->_cache[$moduleName])) {
			/** @var Varien_Simplexml_Element|null $moduleConfig */
			$moduleConfig = Mage::app()->getConfig()->getModuleConfig($moduleName);
			$this->_cache[$moduleName] = $moduleConfig && ('true' === (string)$moduleConfig->{'active'});
			$this->markCachedPropertyAsModified(self::$F__CACHE);
		}
		return $this->_cache[$moduleName];
	}
	/** @var array(string => bool) */
	protected $_cache;

	/**
	 * @override
	 * @return string[]
	 */
	protected function getCacheTagsRm() {return array(Mage_Core_Model_Config::CACHE_TAG);}

	/**
	 * @override
	 * @return string
	 */
	protected function getCacheTypeRm() {return 'config';}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getPropertiesToCachePerStore() {return array(self::$F__CACHE);}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getPropertiesToCacheSimple() {return array(self::$F__CACHE);}

	/** @var string */
	private static $F__CACHE = '_cache';
	/** @return Df_Core_Model_Cache_Module */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}