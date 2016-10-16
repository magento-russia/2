<?php
class Df_Kkb_Model_Action_Confirm extends Df_Payment_Model_Action_Confirm {
	/**
	 * @override
	 * @return void
	 * @throws \Df\Core\Exception
	 */
	protected function checkSignature() {
		/**
		 * Стандартная проверка подписи нам не нужна,
		 * потому что специфическая для Казкоммерцбанка проверка подписи
		 * производится в классе @see Df_Kkb_Model_Response_Payment
		 */
		if (!$this->getResponseAsObject()->isSuccessful()) {
			df_error('Заказ не был оплачен.');
		}
	}

	/**
	 * Использовать @see getConst() нельзя из-за рекурсии.
	 * @override
	 * @return string
	 */
	protected function getRequestKeyOrderIncrementId() {
		// Номер заказа мы получаем не традиционным способом (по ключу в ассоциативном массиве),
		// а через $this->getResponseAsObject()->getOrderIncrementId()
		return 'отсутствует';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getRequestValueOrderIncrementId() {
		return $this->getResponseAsObject()->getOrderIncrementId();
	}
	
	/**
	 * @override
	 * @return string
	 */
	protected function getRequestValuePaymentAmountAsString() {
		return $this->getResponseAsObject()->getPaymentAmountInServiceCurrency()->getAsString();
	}
	
	/** @return Df_Kkb_Model_Response_Payment */
	protected function getResponseAsObject() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Kkb_Model_Response_Payment::i(df_request('response'));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @param Exception $e
	 * @return string
	 */
	protected function getResponseTextForError(Exception $e) {return 0;}

	/**
	 * @override
	 * @return string
	 */
	protected function getResponseTextForSuccess() {return 0;}
	
	/**
	 * Стандартная проверка подписи нам не нужна,
	 * потому что специфическая для Казкоммерцбанка проверка подписи
	 * производится в классе @see Df_Kkb_Model_Response_Payment
	 * @override
	 * @return string
	 */
	protected function getSignatureFromOwnCalculations() {df_should_not_be_here(); return null;}

	/**
	 * @override
	 * @return bool
	 */
	protected function needCapture() {return false;}

	/**
	 * @override
	 * @return void
	 */
	protected function processResponseForSuccess() {
		parent::processResponseForSuccess();
		$this->getResponseAsObject()->postProcess($this->payment());
	}
}