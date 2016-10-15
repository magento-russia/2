<?php
/** @method Df_Kkb_Model_Request_Secondary getRequest() */
class Df_Kkb_Model_RequestDocument_Secondary extends Df_Kkb_Model_RequestDocument_Signed {
	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getLetterAttributes() {return array('id' => $this->configS()->getShopId());}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getLetterBody() {
		return array_filter(array(
			'command' => $this->getDocumentData_Command()
			,'payment' => $this->getDocumentData_Payment()
			,'reason' => $this->getDocumentData_Reason()
		));
	}

	/** @return array(string => mixed) */
	private function getDocumentData_Command() {
		return array(\Df\Xml\X::ATTR => array('type' => $this->getTransactionType()));
	}

	/** @return array(string => mixed) */
	private function getDocumentData_Payment() {
		return array(\Df\Xml\X::ATTR => array(
			'reference' => $this->getRequest()->getPaymentExternalId()
			,'approval_code' => $this->getResponsePayment()->getPaymentCodeApproval()
			,'orderid' => $this->orderIId()
			,'amount' => $this->amount()
			,'currency_code' => $this->getCurrencyCode()
		));
	}

	/** @return string|null */
	private function getDocumentData_Reason() {return $this->isCapture() ? null : 'отмена заказа';}

	/** @return Df_Kkb_Model_Response_Payment */
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
		$this->_prop(self::P__REQUEST, Df_Kkb_Model_Request_Secondary::_C);
	}
	const _C = __CLASS__;
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
				array(
					self::TRANSACTION__CAPTURE =>
						Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE
					,self::TRANSACTION__VOID =>
						Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID
				)
				,$transactionCodeInServiceFormat
			)
		;
		df_result_string_not_empty($result);
		return $result;
	}
	/**
	 * @static
	 * @param Df_Kkb_Model_Request_Secondary $requestSecondary
	 * @return Df_Kkb_Model_RequestDocument_Secondary
	 */
	public static function i(Df_Kkb_Model_Request_Secondary $requestSecondary) {
		return new self(array(self::P__REQUEST => $requestSecondary));
	}
}