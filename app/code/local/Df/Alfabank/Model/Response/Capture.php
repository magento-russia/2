<?php
class Df_Alfabank_Model_Response_Capture extends Df_Alfabank_Model_Response {
	/**
	 * @override
	 * @return array(string => string)
	 */
	public function getReportAsArray() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_clean(array(
				'Успешность запроса' => $this->getErrorCodeMeaning()
				,'Диагностическое сообщение' => $this->onFail($this->getErrorMessage())
			));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	public function getTransactionType() {
		return Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getKey_ErrorCode() {
		return 'errorCode';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getKey_ErrorMessage() {
		return 'errorMessage';
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Alfabank_Model_Response_Capture
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}