<?php
class Df_Ems_Model_Api_Locations_Cities extends Df_Ems_Model_Api_Locations_Abstract {
	/**
	 * @override
	 * @return string
	 */
	protected function getLocationType() {return 'cities';}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Ems_Model_Api_Locations_Cities
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}