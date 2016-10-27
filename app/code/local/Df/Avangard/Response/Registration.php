<?php
namespace Df\Avangard\Response;
use Mage_Sales_Model_Order_Payment_Transaction as T;
class Registration extends \Df\Avangard\Response {
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
	public function getReportAsArray() {return dfc($this, function() {return array_filter([
		'Диагностическое сообщение' => $this->onFail($this->getErrorMessage())
		,'Номер запроса в банке' => $this->getRequestExternalId()
		,'Идентификатор платежа в банке' => $this->getPaymentExternalId()
	]);});}

	/**
	 * @override
	 * @return string
	 */
	public function getTransactionType() {return T::TYPE_PAYMENT;}

	/**
	 * @used-by \Df\Avangard\Action\CustomerReturn::_process()
	 * @used-by \Df\Avangard\Request\Payment::getResponse()
	 * @used-by \Df\Avangard\Request\Secondary::getResponseRegistration()
	 * @param array(string => mixed) $parameters [optional]
	 * @return self
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}