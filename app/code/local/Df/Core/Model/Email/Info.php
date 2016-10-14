<?php
class Df_Core_Model_Email_Info extends Mage_Core_Model_Email_Info {
	const _C = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Core_Model_Email_Info
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}