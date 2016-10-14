<?php
/** @used-by Df_Core_Model_Settings::getMap() */
abstract class Df_Admin_Config_MapItem extends Df_Core_Model {
	/** @return bool */
	abstract public function isValid();

	/**
	 * @param string $class
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Admin_Config_MapItem
	 */
	public static function ic($class, array $parameters = array()) {
		return rm_ic($class, __CLASS__, $parameters);
	}
}