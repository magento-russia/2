<?php
class Df_Checkout_Model_Validator_Ergonomic_Address_Field_Visible
	extends Df_Core_Model_Abstract
	implements Zend_Validate_Interface {
	/**
	 * @override
	 * @return array
	 * @deprecated Since 1.5.0
	 */
	public function getErrors() {
		return array();
	}

	/**
	 * @override
	 * @return array
	 */
	public function getMessages() {
		return array();
	}

	/**
	 * @override
	 * @param Df_Checkout_Block_Frontend_Ergonomic_Address_Field|mixed $value
	 * @return boolean
	 * @throws Zend_Validate_Exception If validation of $value is impossible
	 */
	public function isValid($value) {
		return
				($value instanceof Df_Checkout_Block_Frontend_Ergonomic_Address_Field)
			&&
				$value->needToShow()
		;
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Checkout_Model_Validator_Ergonomic_Address_Field_Visible
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}