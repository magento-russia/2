<?php
class Df_Zf_Validate_String_NotEmpty extends Df_Zf_Validate_Type {
	/**
	 * @override
	 * @param mixed $value
	 * @return bool
	 */
	public function isValid($value) {
		$this->prepareValidation($value);
		return is_string($value) && ('' !== strval($value));
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInAccusativeCase() {return 'непустую строку';}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInGenitiveCase() {return 'непустой строки';}

	/** @return Df_Zf_Validate_String_NotEmpty */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}