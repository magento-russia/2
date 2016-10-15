<?php
class Df_Core_Model_Email_Template_Mailer extends Mage_Core_Model_Email_Template_Mailer {

	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Core_Model_Email_Template_Mailer
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}