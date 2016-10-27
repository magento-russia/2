<?php
namespace Df\Alfabank\Response;
use Mage_Sales_Model_Order_Payment_Transaction as T;
class Void extends \Df\Alfabank\Response {
	/**
	 * @override
	 * @return array(string => string)
	 */
	public function getReportAsArray() {return dfc($this, function() {return array_filter([
		'Успешность запроса' => $this->getErrorCodeMeaning()
		,'Диагностическое сообщение' => $this->onFail($this->getErrorMessage())
	]);});}

	/**
	 * @override
	 * @return string
	 */
	public function getTransactionType() {return T::TYPE_VOID;}

	/**
	 * @override
	 * @return string
	 */
	protected function getKey_ErrorCode() {return 'errorCode';}

	/**
	 * @override
	 * @return string
	 */
	protected function getKey_ErrorMessage() {return 'errorMessage';}
}