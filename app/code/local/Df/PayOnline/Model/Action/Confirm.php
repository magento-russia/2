<?php
class Df_PayOnline_Model_Action_Confirm extends Df_Payment_Model_Action_Confirm {
	/**
	 * @override
	 * @return Df_Payment_Model_Action_Confirm
	 * @throws Mage_Core_Exception
	 */
	protected function checkSignature() {
		if ($this->needInvoice()) {
			parent::checkSignature();
		}
		return $this;
	}

	/**
	 * Использовать getConst нельзя из-за рекурсии.
	 * @override
	 * @return string
	 */
	protected function getRequestKeyOrderIncrementId() {
		return 'OrderId';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getSignatureFromOwnCalculations() {
		/** @var array(string => mixed) $signatureParams */
		$signatureParams =
			array(
				$this->getRequestKeyServicePaymentDate() => $this->getRequestValueServicePaymentDate()
				,$this->getRequestKeyServicePaymentId() => $this->getRequestValueServicePaymentId()
				,$this->getRequestKeyOrderIncrementId() => $this->getRequestValueOrderIncrementId()
				,$this->getRequestKeyPaymentAmount() => $this->getRequestValuePaymentAmountAsString()
				,$this->getRequestKeyPaymentCurrencyCode() => $this->getRequestValuePaymentCurrencyCode()
				,'PrivateSecurityKey' => $this->getResponsePassword()
			)
		;
		/** @var string $result */
		$result =
			md5(
				implode(
					Df_PayOnline_Helper_Data::SIGNATURE_PARTS_SEPARATOR
					,df_h()->payOnline()->preprocessSignatureParams(
						$signatureParams
					)
				)
			)
		;
		return $result;
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param Df_PayOnline_ConfirmController $controller
	 * @return Df_PayOnline_Model_Action_Confirm
	 */
	public static function i(Df_PayOnline_ConfirmController $controller) {
		return new self(array(self::P__CONTROLLER => $controller));
	}
}