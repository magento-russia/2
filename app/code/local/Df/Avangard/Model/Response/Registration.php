<?php
class Df_Avangard_Model_Response_Registration extends Df_Avangard_Model_Response {
	/** @return string */
	public function getPasswordForPaymentResponseError() {return $this->cfg('failure_code');}
	/** @return string */
	public function getPasswordForPaymentResponseSuccess() {return $this->cfg('ok_code');}
	/** @return string */
	public function getPaymentExternalId() {return $this->cfg('ticket');}

	/**
	 * @override
	 * @return array(string => string)
	 */
	public function getReportAsArray() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_clean(array(
				'Диагностическое сообщение' => $this->onFail($this->getErrorMessage())
				,'Номер запроса в банке' => $this->getRequestExternalId()
				,'Идентификатор платежа в банке' => $this->getPaymentExternalId()
			));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	public function getTransactionType() {return Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT;}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Avangard_Model_Response_Registration
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}