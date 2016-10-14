<?php
class Df_Zf_Validate_Boolean extends Df_Zf_Validate_Type implements Zend_Filter_Interface {
	/**
	 * @override
	 * @param  mixed $value
	 * @throws Zend_Filter_Exception
	 * @return bool
	 */
	public function filter($value) {
		/** @var bool $result */
		try {
			$result = rm_bool($value);
		}
		catch (Exception $e) {
			df_error(new Zend_Filter_Exception(rm_ets($e)));
		}
		return $result;
	}

	/**
	 * @override
	 * @param mixed $value
	 * @return bool
	 */
	public function isValid($value) {
		$this->prepareValidation($value);
		return is_bool($value);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInAccusativeCase() {
		return 'значение логического типа («да/нет»)';
	}
	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInGenitiveCase() {
		return 'значения логического типа («да/нет»)';
	}

	/** @return Df_Zf_Validate_Boolean */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}