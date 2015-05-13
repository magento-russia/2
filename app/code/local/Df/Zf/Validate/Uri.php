<?php
class Df_Zf_Validate_Uri extends Df_Zf_Validate_Type {
	/**
	 * @override
	 * @param string|integer $value
	 * @return boolean
	 */
	public function isValid($value) {
		$this->prepareValidation($value);
		return Zend_Uri::check($value);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInAccusativeCase() {return 'веб-адрес';}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInGenitiveCase() {return 'веб-адреса';}

	/** @return Df_Zf_Validate_Uri */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}