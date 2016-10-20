<?php
class Df_EasyPay_Action_Confirm extends Df_Payment_Model_Action_Confirm {
	/**
	 * @override
	 * @return void
	 * @throws \Df\Core\Exception
	 */
	protected function checkPaymentAmount() {
		if (
				$this->rAmount()->getAsInteger()
			!==
				$this->getPaymentAmountFromOrder()->getAsInteger()
		) {
			$this->errorInvalidAmount();
		}
	}

	/**
	 * Использовать getConst нельзя из-за рекурсии.
	 * @override
	 * @return string
	 */
	protected function getRequestKeyOrderIncrementId() {return 'order_mer_code';}

	/**
	 * @override
	 * @return string
	 */
	protected function getSignatureFromOwnCalculations() {
		/** @var string[] $signatureParams */
		$signatureParams = array(
			$this->getRequestValueOrderIncrementId()
			,/**
			 * Обратите внимание, что хотя размер платежа всегда является целым числом,
			 * но EasyPay присылает его в формате с двумя знаками после запятой.
			 * Например: «103.00», а не «103».
			 *
			 * Поэтому не используем $this->rAmount()->getAsInteger()
			 */
			$this->getRequest()->getParam('sum')
			,$this->getRequestValueShopId()
			,$this->getRequest()->getParam('card')
			,$this->getRequestValueServicePaymentDate()
			,$this->getResponsePassword()
		);
		return md5(implode($signatureParams));
	}

	/**
	 * @override
	 * @param Exception $e
	 * @return void
	 */
	protected function processException(Exception $e) {
		parent::processException($e);
		/**
		 * В случае, если Поставщик не может по техническим или другим причинам обработать уведомление,
		 * он должен ответить любым кодом ошибки, например "HTTP/1.0 400 Bad Request".
		 * Недопустимо отвечать кодом "HTTP/1.0 200 OK" на необработанное уведомление.
		 * https://ssl.easypay.by/notify/
		 */
		$this->getResponse()->setHttpResponseCode(500);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function processResponseForSuccess() {
		parent::processResponseForSuccess();
		/**
		 * Уведомление Поставщика о совершенном платеже осуществляется запросом,
		 * который будет отсылаться до тех пор, пока Поставщик его не примет,
		 * то есть не ответит ему кодом "HTTP/1.0 200 OK".
		 * https://ssl.easypay.by/notify/
		 */
		$this->getResponse()->setRawHeader('HTTP/1.0 200 OK');
	}
}