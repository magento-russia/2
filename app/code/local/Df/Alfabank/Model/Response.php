<?php
abstract class Df_Alfabank_Model_Response extends Df_Payment_Model_Response {
	/** @return string */
	abstract protected function getKey_ErrorCode();

	/** @return string */
	abstract protected function getKey_ErrorMessage();

	/** @return int */
	public function getErrorCode() {return rm_int($this->cfg($this->getKey_ErrorCode()));}

	/** @return string */
	public function getErrorCodeMeaning() {
		return df_a($this->getErrorCodeMap(), $this->getErrorCode(), 'Неизвестно');
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getErrorMessage() {return strval($this->cfg($this->getKey_ErrorMessage()));}

	/**
	 * @override
	 * @return bool
	 */
	protected function isSuccessful() {return 0 === $this->getErrorCode();}

	/** @return array(int => string) */
	protected function getErrorCodeMap() {
		return
			array(
				0 => 'Обработка запроса прошла без системных ошибок'
				,5 => 'Ошибка значения параметра запроса'
				,6 => 'Незарегистрированный OrderId'
				,7 => 'Системная ошибка'
			)
		;
	}

	const _CLASS = __CLASS__;
}