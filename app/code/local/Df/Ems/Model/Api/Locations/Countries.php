<?php
class Df_Ems_Model_Api_Locations_Countries extends Df_Ems_Model_Api_Locations_Abstract {
	/**
	 * @override
	 * @return string
	 */
	protected function getLocationType() {return 'countries';}

	const _C = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Ems_Model_Api_Locations_Countries
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}