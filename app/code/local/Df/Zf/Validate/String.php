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
		/**
		 * 2015-02-16
		 * Раньше здесь стояло просто is_string($value)
		 * Однако интерпретатор PHP способен неявно и вполне однозначно
		 * (без двусмысленностей, как, скажем, с вещественными числами)
		 * конвертировать целые числа и null в строки,
		 * поэтому пусть целые числа и null всегда проходят валидацию как строки.
		 */
		return is_string($value) || is_int($value) || is_null($value);
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