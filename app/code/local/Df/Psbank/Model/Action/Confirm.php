<?php
abstract class Df_Psbank_Model_Action_Confirm extends Df_Payment_Model_Action_Confirm {
	/** @return string[] */
	abstract protected function getParamsForSignature();

	/**
	 * Использовать getConst нельзя из-за рекурсии.
	 * @override
	 * @return string
	 */
	protected function getRequestKeyOrderIncrementId() {return 'ORDER';}
	
	/** @return Df_Psbank_Model_Response */
	protected function getResponseAsObject() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Psbank_Model_Response::i($this->getRequest()->getParams());
			$this->{__METHOD__}->postProcess($this->getPayment());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getResponseTextForSuccess() {return 'OK';}
	
	/**
	 * @override
	 * @return string
	 */
	protected function getSignatureFromOwnCalculations() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * Опосредовано вызывает @see Df_Payment_Model_Response::postProcess()
			 * Лучшего способа вызвать postProcess,
			 * чем запихнуть в getSignatureFromOwnCalculations() — не придумал.
			 */
			$this->getResponseAsObject();
			$this->{__METHOD__} = Df_Psbank_Helper_Data::s()->generateSignature(
				$this->getRequest()->getParams()
				,$this->getParamsForSignature()
				,$this->configS()->getRequestPassword()
			);
		}
		return $this->{__METHOD__};
	}

	const _C = __CLASS__;
}