<?php
class Df_Dataflow_Model_Convert_Profile extends Mage_Dataflow_Model_Convert_Profile {
	const _C = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Dataflow_Model_Convert_Profile
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}