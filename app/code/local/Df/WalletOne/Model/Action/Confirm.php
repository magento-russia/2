<?php
class Df_WalletOne_Model_Action_Confirm extends Df_Payment_Model_Action_Confirm {
	/**
	 * @override
	 * @return Df_WalletOne_Model_Action_Confirm
	 */
	protected function alternativeProcessWithoutInvoicing() {
		parent::alternativeProcessWithoutInvoicing();
		$this->getOrder()->addStatusHistoryComment('Покупатель отказался от оплаты');
		$this->getOrder()->setData(Df_Sales_Const::ORDER_PARAM__IS_CUSTOMER_NOTIFIED, false);
		$this->getOrder()->save();
		return $this;
	}

	/**
	 * Использовать getConst нельзя из-за рекурсии.
	 * @override
	 * @return string
	 */
	protected function getRequestKeyOrderIncrementId() {
		return 'WMI_PAYMENT_NO';
	}

	/**
	 * @override
	 * @param Exception $e
	 * @return string
	 */
	protected function getResponseTextForError(Exception $e) {
		return rm_sprintf('WMI_RESULT=CANCEL&WMI_DESCRIPTION=%s', urlencode(rm_ets($e)));
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getResponseTextForSuccess() {
		return 'WMI_RESULT=OK';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getSignatureFromOwnCalculations() {
		return $this->getSignatureGenerator()->getSignature();
	}

	/**
	 * @override
	 * @return bool
	 */
	protected function needInvoice() {
		return 'accepted' === mb_strtolower($this->getRequestValueServicePaymentState());
	}

	/**
	 * @override
	 * @return Df_Payment_Model_Action_Confirm
	 * @throws Mage_Core_Exception
	 */
	protected function processOrderCanNotInvoice() {
		/**
		 * Единая Касса любит присылать повторные оповещения об оплате.
		 */
		$this->getOrder()->addStatusHistoryComment('Единая Касса повторно прислала оповещение об оплате');
		$this->getResponse()->setBody($this->getResponseTextForSuccess());
		return $this;
	}

	/** @return Df_WalletOne_Model_Request_SignatureGenerator */
	private function getSignatureGenerator() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_WalletOne_Model_Request_SignatureGenerator::i(array(
				Df_WalletOne_Model_Request_SignatureGenerator::P__ENCRYPTION_KEY =>
					$this->getServiceConfig()->getResponsePassword()
				,Df_WalletOne_Model_Request_SignatureGenerator::P__SIGNATURE_PARAMS =>
					array_diff_key(
						$this->getRequest()->getParams()
						,array($this->getRequestKeySignature() => null)
					)
			));
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param Df_WalletOne_ConfirmController $controller
	 * @return Df_WalletOne_Model_Action_Confirm
	 */
	public static function i(Df_WalletOne_ConfirmController $controller) {
		return new self(array(self::P__CONTROLLER => $controller));
	}
}