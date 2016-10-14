<?php
class Df_Zf_Validate_Int extends Df_Zf_Validate_Type implements Zend_Filter_Interface {
	/**
	 * @override
	 * @param  mixed $value
	 * @throws Zend_Filter_Exception
	 * @return int
	 */
	public function filter($value) {
		/** @var int $result */
		try {
			$result = df_int($value, $allowNull = true);
		}
		catch (Exception $e) {
			df_error(new Zend_Filter_Exception(df_ets($e)));
		}
		return $result;
	}

	/**
	 * @override
	 * @param string|integer $value
	 * @return boolean
	 */
	public function isValid($value) {
		$this->prepareValidation($value);
		/**
		 * Обратите внимание, что здесь нужно именно «==», а не «===».
		 * http://php.net/manual/function.is-int.php#35820
		 */
		return is_numeric($value) && ($value == (int)$value);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInAccusativeCase() {return 'целое число';}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInGenitiveCase() {return 'целого числа';}

	/** @return Df_Zf_Validate_Int */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}