<?php
class Df_Ems_Api_Locations_Cities extends Df_Ems_Api_Locations {
	/**
	 * @override
	 * @return string
	 */
	protected function getLocationType() {return 'cities';}

	const _C = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Ems_Api_Locations_Cities
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}