<?php
class Df_Interkassa_Action_Confirm extends Df_Payment_Model_Action_Confirm {
	/**
	 * @override
	 * @return void
	 */
	protected function alternativeProcessWithoutInvoicing() {
		$this->comment($this->getPaymentStateMessage($this->rState()));
	}

	/**
	 * Использовать getConst нельзя из-за рекурсии.
	 * @override
	 * @return string
	 */
	protected function rkOII() {return 'ik_payment_id';}

	/**
	 * @override
	 * @return string
	 */
	protected function signatureOwn() {
		/** @var string[] $signatureParams */
		$signatureParams = array(
			$this->rShopId()
			,$this->rAmountS()
			,$this->rOII()
			,$this->param('ik_paysystem_alias')
			,$this->param('ik_baggage_fields')
			,$this->rState()
			,$this->rExternalId()
			,$this->param('ik_currency_exch')
			,$this->param('ik_fees_payer')
			,$this->getResponsePassword()
		);
		/** @var string $result */
		$result = strtoupper(md5(implode(self::SIGNATURE_PARTS_SEPARATOR, $signatureParams)));
		return $result;
	}

	/**
	 * Как я понял,
	 * при оплате электронной валютой платёжная система сразу отсылает статус «paid»,
	 * а при оплате банковской картой — сначала «authorized», и лишь потом — «paid».
	 * @override
	 * @return bool
	 */
	protected function needInvoice() {
		return
				$this->order()->canInvoice()
			&&
				self::PAYMENT_STATE__PAID === $this->rState()
		;
	}

	/**
	 * @param string $code
	 * @return string
	 */
	private function getPaymentStateMessage($code) {
		df_param_string_not_empty($code, 0);
		/** @var string $result */
		$result =
			dfa(
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

	const PAYMENT_STATE__PAID = 'success';
	const PAYMENT_STATE__CANCELED = 'fail';
	const SIGNATURE_PARTS_SEPARATOR = ':';
}