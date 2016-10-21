<?php
class Df_Kkb_Action_Confirm extends Df_Payment_Model_Action_Confirm {
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
	protected function rkOII() {
		// Номер заказа мы получаем не традиционным способом (по ключу в ассоциативном массиве),
		// а через $this->getResponseAsObject()->getOrderIncrementId()
		return 'отсутствует';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function rOII() {
		return $this->getResponseAsObject()->getOrderIncrementId();
	}
	
	/**
	 * @override
	 * @return string
	 */
	protected function rAmountS() {
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
	protected function responseTextForError(Exception $e) {return 0;}

	/**
	 * @override
	 * @return string
	 */
	protected function responseTextForSuccess() {return 0;}
	
	/**
	 * Стандартная проверка подписи нам не нужна,
	 * потому что специфическая для Казкоммерцбанка проверка подписи
	 * производится в классе @see Df_Kkb_Model_Response_Payment
	 * @override
	 * @return string
	 */
	protected function signatureOwn() {df_should_not_be_here(); return null;}

	/**
	 * @override
	 * @return bool
	 */
	protected function needCapture() {return false;}

	/**
	 * @override
	 * @see Df_Payment_Model_Action_Confirm::processResponseForSuccess()
	 * @used-by _process()
	 * @return void
	 */
	protected function processResponseForSuccess() {
		parent::processResponseForSuccess();
		$this->getResponseAsObject()->postProcess($this->payment());
	}
}