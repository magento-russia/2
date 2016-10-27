<?php
class Df_PayOnline_Action_Confirm extends \Df\Payment\Action\Confirm {
	/**
	 * @override
	 * @return void
	 * @throws Mage_Core_Exception
	 */
	protected function checkSignature() {
		if ($this->needInvoice()) {
			parent::checkSignature();
		}
	}

	/**
	 * Использовать @see getConst() нельзя из-за рекурсии.
	 * @override
	 * @return string
	 */
	protected function rkOII() {return 'OrderId';}

	/**
	 * @override
	 * @return string
	 */
	protected function signatureOwn() {
		/** @var array(string => mixed) $signatureParams */
		$signatureParams = [
			$this->rkTime() => $this->rTime()
			,$this->rkExternalId() => $this->rExternalId()
			,$this->rkOII() => $this->rOII()
			,$this->rkAmount() => $this->rAmountS()
			,$this->rkCurrency() => $this->rCurrencyC()
			,'PrivateSecurityKey' => $this->getResponsePassword()
		];
		/** @var string $result */
		$result = md5(implode(
			Df_PayOnline_Helper_Data::SIGNATURE_PARTS_SEPARATOR
			,df_h()->payOnline()->preprocessSignatureParams($signatureParams)
		));
		return $result;
	}
}