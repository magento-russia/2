<?php
class Df_Core_Model_Geo_Cache extends Df_Core_Model {
	/**
	 * @override
	 * @see Df_Core_Model::cacheLifetime()
	 * @used-by Df_Core_Model::cacheSaveProperty()
	 * @return int|null
	 */
	protected function cacheLifetime() {return 86400 * 7;}

	/**
	 * @override
	 * @see Df_Core_Model::cachedGlobal()
	 * @return string[]
	 */
	protected function cachedGlobal() {return self::m(__CLASS__, 'cache');}

	/**
	 * @used-by Df_Core_Model_Geo_Locator::loadFromCache()
	 * @used-by Df_Core_Model_Geo_Locator::saveToCache()
	 * @var array(string => array(string => string))
	 */
	public $cache = [];

	/** @return Df_Core_Model_Geo_Cache */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}