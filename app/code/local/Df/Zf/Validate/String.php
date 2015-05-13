<?php
class Df_Zf_Validate_String extends Df_Zf_Validate_Type implements Zend_Filter_Interface {
	/**
	 * @override
	 * @param mixed $value
	 * @throws Zend_Filter_Exception
	 * @return string|mixed
	 */
	public function filter($value) {
		return is_null($value) || is_int($value) ? strval($value) : $value;
	}

	/**
	 * @override
	 * @param mixed $value
	 * @return bool
	 */
	public function isValid($value) {
		$this->prepareValidation($value);
		return is_string($value);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInAccusativeCase() {return 'строку';}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInGenitiveCase() {return 'строки';}

	/** @return Df_Zf_Validate_String */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}