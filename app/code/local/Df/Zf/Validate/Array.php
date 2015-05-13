<?php
class Df_Zf_Validate_Array extends Df_Zf_Validate_Type {
	/**
	 * @override
	 * @param mixed $value
	 * @return bool
	 */
	public function isValid($value) {
		$this->prepareValidation($value);
		return is_array($value);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInAccusativeCase() {return 'массив';}
	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInGenitiveCase() {return 'массива';}

	/** @return Df_Zf_Validate_Array */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}