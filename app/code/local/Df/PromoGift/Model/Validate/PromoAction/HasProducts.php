<?php
class Df_PromoGift_Model_Validate_PromoAction_HasProducts
	extends Df_Core_Model_Abstract
	implements Zend_Validate_Interface {
	const _CLASS = __CLASS__;

	/**
	 * Returns an array of message codes that explain why a previous isValid() call
	 * returned false.
	 *
	 * If isValid() was never called or if the most recent isValid() call
	 * returned true, then this method returns an empty array.
	 *
	 * This is now the same as calling array_keys() on the return value from getMessages().
	 * @return array
	 * @deprecated Since 1.5.0
	 */
	public function getErrors() {return array();}

	/**
	 * @param Df_PromoGift_Model_PromoAction|mixed $value
	 * @return boolean
	 * @throws Zend_Validate_Exception If validation of $value is impossible
	 */
	public function isValid($value) {
		return $value instanceof Df_PromoGift_Model_PromoAction;
	}

	/** @return array */
	public function getMessages() {return array();}
}