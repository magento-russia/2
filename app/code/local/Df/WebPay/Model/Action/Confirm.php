<?php
class Df_WebPay_Model_Action_Confirm extends Df_Payment_Model_Action_Confirm {
	/**
	 * @override
	 * @return void
	 */
	protected function alternativeProcessWithoutInvoicing() {
		$this->order()->comment(
			$this->getPaymentStateMessage(df_int($this->getRequestValueServicePaymentState()))
		);
	}

	/**
	 * @override
	 * @return void
	 * @throws Mage_Core_Exception
	 */
	protected function checkPaymentAmount() {
		if (
				$this->getRequestValuePaymentAmount()->getAsInteger()
			!==
				$this->getPaymentAmountFromOrder()->getAsInteger()
		) {
			df_error(
				$this->getMessage(self::CONFIG_KEY__MESSAGE__INVALID__PAYMENT_AMOUNT)
				,$this->getPaymentAmountFromOrder()->getAsInteger()
				,$this->configS()->getCurrencyCode()
				,$this->getRequestValuePaymentAmount()->getAsInteger()
				,$this->configS()->getCurrencyCode()
			);
		}
	}

	/**
	 * Использовать getConst нельзя из-за рекурсии.
	 * @override
	 * @return string
	 */
	protected function getRequestKeyOrderIncrementId() {return 'site_order_id';}

	/**
	 * @override
	 * @return string
	 */
	protected function getSignatureFromOwnCalculations() {
		/** @var string[] $signatureParams */
		$signatureParams = array(
			$this->getRequestValueServicePaymentDate()
			,$this->getRequestValuePaymentCurrencyCode()
			,$this->getRequestValuePaymentAmount()->getAsInteger()
			,$this->getRequest()->getParam('payment_method')
			,$this->getRequest()->getParam('order_id')
			,$this->getRequestValueOrderIncrementId()
			,$this->getRequestValueServicePaymentId()
			,$this->getRequestValueServicePaymentState()
			,$this->getRequest()->getParam('rrn')
			,$this->getResponsePassword()
		);
		return md5(implode($signatureParams));
	}

	/**
	 * @override
	 * @return bool
	 */
	protected function needInvoice() {
		return in_array(
			$this->getRequestValueServicePaymentState()
			,array(self::PAYMENT_STATE__AUTHORIZED, self::PAYMENT_STATE__COMPLETED)
		);
	}

	/**
	 * @override
	 * @param Exception $e
	 * @return Df_WebPay_Model_Action_Confirm
	 */
	protected function processException(Exception $e) {
		parent::processException($e);
		/**
		 * Интернет-ресурс в случае оповещения, должен ответить кодом 200 ("HTTP/1.0 200 OK"),
		 * если сервер интернет-ресурса не отвечает положительно,
		 * а с момента начала отсылки уведомляющих запросов прошел 1 час,
		 * на адрес магазина отсылается электронное письмо,
		 * предупреждающее о сбое.
		 * Через 30 дней, если интернет-ресурс так и не смог принять уведомление,
		 * отсылка запросов прекращается, о чем интернет-ресурс также извещается электронным письмом.
		 * https://mail.google.com/mail/u/0/?ui=2&ik=a7a1e9bc54&view=att&th=135800f28b66a0b2&attid=0.0&disp=inline&safe=1&zw
		 */
		$this->getResponse()->setHttpResponseCode(500);
		return $this;
	}

	/**
	 * @override
	 * @return Df_Payment_Model_Action_Confirm
	 */
	protected function processResponseForSuccess() {
		parent::processResponseForSuccess();
		/**
		 * Интернет-ресурс в случае оповещения, должен ответить кодом 200 ("HTTP/1.0 200 OK")
		 * https://mail.google.com/mail/u/0/?ui=2&ik=a7a1e9bc54&view=att&th=135800f28b66a0b2&attid=0.0&disp=inline&safe=1&zw
		 */
		$this->getResponse()->setRawHeader('HTTP/1.0 200 OK');
		return $this;
	}

	/**
	 * @param int $code
	 * @return string
	 */
	private function getPaymentStateMessage($code) {
		df_param_integer($code, 0);
		/** @var string $result */
		$result =
			dfa(
				array(
					self::PAYMENT_STATE__COMPLETED => 'completed'
					,self::PAYMENT_STATE__DECLINED => 'declined'
					,self::PAYMENT_STATE__PENDING => 'pending'
					,self::PAYMENT_STATE__AUTHORIZED => 'authorized'
					,self::PAYMENT_STATE__REFUNDED => 'refunded'
					,self::PAYMENT_STATE__SYSTEM => 'system'
					,self::PAYMENT_STATE__VOIDED => 'voided'
				)
				,$code
			)
		;
		df_result_string($result);
		return $result;
	}

	const PAYMENT_STATE__COMPLETED = 1;
	const PAYMENT_STATE__DECLINED = 2;
	const PAYMENT_STATE__PENDING = 3;
	const PAYMENT_STATE__AUTHORIZED = 4;
	const PAYMENT_STATE__REFUNDED = 5;
	const PAYMENT_STATE__SYSTEM = 6;
	const PAYMENT_STATE__VOIDED = 7;
}