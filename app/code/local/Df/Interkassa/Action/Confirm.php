<?php
namespace Df\Interkassa\Action;
class Confirm extends \Df\Payment\Action\Confirm {
	/**
	 * @override
	 * @return void
	 */
	protected function alternativeProcessWithoutInvoicing() {
		$this->comment(dfa([
			self::PAID => 'Оплата получена'
			,'fail' => 'Покупатель отказался от оплаты']
		, $this->rState()));
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
	protected function signatureOwn() {return strtoupper(md5(implode(':', [
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
	])));}

	/**
	 * Как я понял,
	 * при оплате электронной валютой платёжная система сразу отсылает статус «paid»,
	 * а при оплате банковской картой — сначала «authorized», и лишь потом — «paid».
	 * @override
	 * @return bool
	 */
	protected function needInvoice() {return
		$this->order()->canInvoice() && self::PAID === $this->rState()
	;}

	const PAID = 'success';
}