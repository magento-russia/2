<?php
class Df_Eav_Model_Entity extends Mage_Eav_Model_Entity {

	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Eav_Model_Entity
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}

	/** @return Df_Eav_Model_Entity */
	public static function product() {return self::getSingletonByType('catalog_product');}

	/**
	 * @param string $type
	 * @return Df_Eav_Model_Entity
	 */
	private static function getSingletonByType($type) {
		/** @var array(string => Df_Eav_Model_Entity) */
		static $cache;
		if (!isset($cache[$type])) {
			$cache[$type] = self::i()->setType($type);
		}
		return $cache[$type];
	}
}