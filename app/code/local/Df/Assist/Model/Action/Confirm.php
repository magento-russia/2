<?php
class Df_Assist_Model_Action_Confirm extends Df_Payment_Model_Action_Confirm {
	/**
	 * Использовать getConst нельзя из-за рекурсии.
	 * @override
	 * @return string
	 */
	protected function getRequestKeyOrderIncrementId() {
		return 'ordernumber';
	}

	/**
	 * @override
	 * @param Exception $e
	 * @return string
	 */
	protected function getResponseTextForError(Exception $e) {
		return $this->getResponseBlockForError($e)->toHtml();
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getResponseTextForSuccess() {
		return $this->getResponseBlockForSuccess()->toHtml();
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getSignatureFromOwnCalculations() {
		/** @var string $result */
		$result =
			strtoupper(
				md5(
					strtoupper(
						df_concat(
							md5($this->getResponsePassword())
							,md5(
								df_concat(
									$this->getRequestValueShopId()
									,$this->getRequestValueOrderIncrementId()
									,$this->getRequestValuePaymentAmountAsString()
									,$this->getRequestValuePaymentCurrencyCode()
									,$this->getRequestValueServicePaymentState()
								)
							)
						)
					)
				)
			)
		;
		return $result;
	}

	/**
	 * @param Exception $e
	 * @return Df_Assist_Block_Api_PaymentConfirmation_Error
	 */
	private function getResponseBlockForError(Exception $e) {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Assist_Block_Api_PaymentConfirmation_Error::i();
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Assist_Block_Api_PaymentConfirmation_Success */
	private function getResponseBlockForSuccess() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Assist_Block_Api_PaymentConfirmation_Success::i(
					$this->getRequestValueServicePaymentId()
					, $this->getRequestValueServicePaymentDate()
				)
			;
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param Df_Assist_ConfirmController $controller
	 * @return Df_Assist_Model_Action_Confirm
	 */
	public static function i(Df_Assist_ConfirmController $controller) {
		return new self(array(self::P__CONTROLLER => $controller));
	}
}