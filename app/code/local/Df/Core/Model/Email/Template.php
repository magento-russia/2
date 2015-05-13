<?php
class Df_Core_Model_Email_Template extends Mage_Core_Model_Email_Template {
	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Core_Model_Email_Template
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}