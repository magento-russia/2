<?php
class Df_Core_Model_Cache_Design_Package extends Df_Core_Model_DestructableSingleton {
	/**
	 * @param string $key
	 * @return string|null
	 */
	public function cacheGet($key) {return df_a($this->_cache, $key);}

	/**
	 * @param string $key
	 * @param string $value
	 * @return void
	 */
	public function cacheSet($key, $value) {
		$this->_cache[$key] = $value;
		$this->markCachedPropertyAsModified(self::$F__CACHE);
	}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getPropertiesToCache() {return array(self::$F__CACHE);}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getPropertiesToCacheSimple() {return array(self::$F__CACHE);}

	/** @var array(string => string) */
	protected $_cache = array();
	/** @var string */
	private static $F__CACHE = '_cache';

	/** @return Df_Core_Model_Cache_Design_Package */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}