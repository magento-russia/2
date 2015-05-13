<?php
class Df_Interkassa_Model_Action_Confirm extends Df_Payment_Model_Action_Confirm {
	/**
	 * @override
	 * @return Df_Interkassa_Model_Action_Confirm
	 */
	protected function alternativeProcessWithoutInvoicing() {
		parent::alternativeProcessWithoutInvoicing();
		$this->getOrder()
			->addStatusHistoryComment(
				$this->getPaymentStateMessage(
					$this->getRequestValueServicePaymentState()
				)
			)
		;
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
		return 'ik_payment_id';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getSignatureFromOwnCalculations() {
		/** @var string[] $signatureParams */
		$signatureParams =
			array(
				$this->getRequestValueShopId()
				,$this->getRequestValuePaymentAmountAsString()
				,$this->getRequestValueOrderIncrementId()
				,$this->getRequest()->getParam('ik_paysystem_alias')
				,$this->getRequest()->getParam('ik_baggage_fields')
				,$this->getRequestValueServicePaymentState()
				,$this->getRequestValueServicePaymentId()
				,$this->getRequest()->getParam('ik_currency_exch')
				,$this->getRequest()->getParam('ik_fees_payer')
				,$this->getResponsePassword()
			)
		;
		/** @var string $result */
		$result = strtoupper(md5(implode(self::SIGNATURE_PARTS_SEPARATOR, $signatureParams)));
		return $result;
	}

	/**
	 * @override
	 * @return bool
	 */
	protected function needInvoice() {
		/**
		 * Дело в том, что, как я понял,
		 * в случае оплаты покупателем заказа электронной валютой,
		 * платёжная система сразу отсылает статус «paid»,
		 * а в случае оплаты банковской картой —
		 * сначала «authorized», и лишь потом — «paid».
		 */
		return
				$this->getOrder()->canInvoice()
			&&
				(self::PAYMENT_STATE__PAID === $this->getRequestValueServicePaymentState())
		;
	}

	/**
	 * @param string $code
	 * @return string
	 */
	private function getPaymentStateMessage($code) {
		df_param_string($code, 0);
		/** @var string $result */
		$result =
			df_a(
				array(
					self::PAYMENT_STATE__PAID => 'Оплата получена'
					,self::PAYMENT_STATE__CANCELED => 'Покупатель отказался от оплаты'
				)
				,$code
			)
		;
		df_result_string($result);
		return $result;
	}

	const _CLASS = __CLASS__;
	const PAYMENT_STATE__PAID = 'success';
	const PAYMENT_STATE__CANCELED = 'fail';
	const SIGNATURE_PARTS_SEPARATOR = ':';
	/**
	 * @static
	 * @param Df_Interkassa_ConfirmController $controller
	 * @return Df_Interkassa_Model_Action_Confirm
	 */
	public static function i(Df_Interkassa_ConfirmController $controller) {
		return new self(array(self::P__CONTROLLER => $controller));
	}
}