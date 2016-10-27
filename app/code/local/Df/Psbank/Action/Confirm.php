<?php
abstract class Df_Psbank_Action_Confirm extends \Df\Payment\Action\Confirm {
	/** @return string[] */
	abstract protected function getParamsForSignature();

	/**
	 * Использовать getConst нельзя из-за рекурсии.
	 * @override
	 * @return string
	 */
	protected function rkOII() {return 'ORDER';}
	
	/** @return Df_Psbank_Response */
	protected function getResponseAsObject() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Psbank_Response::i($this->params());
			$this->{__METHOD__}->postProcess($this->payment());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function responseTextForSuccess() {return 'OK';}
	
	/**
	 * @override
	 * @return string
	 */
	protected function signatureOwn() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * Опосредовано вызывает @see \Df\Payment\Response::postProcess()
			 * Лучшего способа вызвать postProcess,
			 * чем запихнуть в signatureOwn() — не придумал.
			 */
			$this->getResponseAsObject();
			$this->{__METHOD__} = Df_Psbank_Helper_Data::s()->generateSignature(
				$this->params()
				,$this->getParamsForSignature()
				,$this->configS()->getRequestPassword()
			);
		}
		return $this->{__METHOD__};
	}


}