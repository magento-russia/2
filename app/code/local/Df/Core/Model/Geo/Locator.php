<?php
abstract class Df_Core_Model_Geo_Locator extends Df_Core_Model {
	/** @return string */
	protected function getIpAddress() {return $this->cfg(self::$P__IP_ADDRESS);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__IP_ADDRESS, DF_V_STRING_NE);
	}
	/** @var string */
	private static $P__IP_ADDRESS = 'is_address';

	/**
	 * @param string $class
	 * @param string $ipAddress
	 * @return Df_Core_Model_Geo_Locator
	 */
	protected static function sc($class, $ipAddress) {
		/** @var array(string => array(string => Df_Core_Model_Geo_Locator)) */
		static $cache = array();
		if (!isset($cache[$class][$ipAddress])) {
			/** @var Df_Core_Model_Geo_Locator $locator */
			$locator = new $class(array(self::$P__IP_ADDRESS => $ipAddress));
			df_assert($locator instanceof Df_Core_Model_Geo_Locator);
			$cache[$class][$ipAddress] = $locator;
		}
		return $cache[$class][$ipAddress];
	}
}