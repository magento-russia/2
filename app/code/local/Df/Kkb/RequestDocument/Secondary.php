<?php
namespace Df\Kkb\RequestDocument;
use Df\Xml\X as X;
use Mage_Sales_Model_Order_Payment_Transaction as T;
/** @method \Df\Kkb\Request\Secondary getRequest() */
class Secondary extends Signed {
	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getLetterAttributes() {return ['id' => $this->configS()->getShopId()];}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getLetterBody() {return array_filter([
		'command' => $this->getDocumentData_Command()
		,'payment' => $this->getDocumentData_Payment()
		,'reason' => $this->getDocumentData_Reason()
	]);}

	/** @return array(string => mixed) */
	private function getDocumentData_Command() {return [
		X::ATTR => ['type' => $this->getTransactionType()]
	];}

	/** @return array(string => mixed) */
	private function getDocumentData_Payment() {return [
		X::ATTR => [
			'reference' => $this->getRequest()->getPaymentExternalId()
			,'approval_code' => $this->getResponsePayment()->getPaymentCodeApproval()
			,'orderid' => $this->orderIId()
			,'amount' => $this->amount()
			,'currency_code' => $this->getCurrencyCode()
		]
	];}

	/** @return string|null */
	private function getDocumentData_Reason() {return $this->isCapture() ? null : 'отмена заказа';}

	/** @return \Df\Kkb\Response\Payment */
	private function getResponsePayment() {return $this->getRequest()->getResponsePayment();}

	/** @return string */
	private function getTransactionType() {return $this->getRequest()->getTransactionType();}

	/** @return bool */
	private function isCapture() {return self::TRANSACTION__CAPTURE === $this->getTransactionType();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__REQUEST, \Df\Kkb\Request\Secondary::class);
	}

	const TRANSACTION__CAPTURE = 'complete';
	const TRANSACTION__VOID = 'reverse';
	/**
	 * @param string $transactionCodeInServiceFormat
	 * @return string
	 */
	public static function convertTransactionCodeToMagentoFormat($transactionCodeInServiceFormat) {
		df_param_string_not_empty($transactionCodeInServiceFormat, 0);
		/** @var string $result */
		$result =
			dfa(
				[
					self::TRANSACTION__CAPTURE => T::TYPE_CAPTURE
					, self::TRANSACTION__VOID => T::TYPE_VOID
				]
				,$transactionCodeInServiceFormat
			)
		;
		df_result_string_not_empty($result);
		return $result;
	}
	/**
	 * @static
	 * @param \Df\Kkb\Request\Secondary $requestSecondary
	 * @return \Df\Kkb\RequestDocument\Secondary
	 */
	public static function i(\Df\Kkb\Request\Secondary $requestSecondary) {return
		new self([self::P__REQUEST => $requestSecondary])
	;}
}