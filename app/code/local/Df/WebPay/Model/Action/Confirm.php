<?php
class Df_WebPay_Model_Action_Confirm extends Df_Payment_Model_Action_Confirm {
	/**
	 * @override
	 * @return Df_WebPay_Model_Action_Confirm
	 */
	protected function alternativeProcessWithoutInvoicing() {
		parent::alternativeProcessWithoutInvoicing();
		$this->getOrder()->addStatusHistoryComment(
			$this->getPaymentStateMessage(
				rm_int($this->getRequestValueServicePaymentState())
			)
		);
		$this->getOrder()->setData(Df_Sales_Const::ORDER_PARAM__IS_CUSTOMER_NOTIFIED, false);
		$this->getOrder()->save();
		return $this;
	}

	/**
	 * @override
	 * @return Df_Payment_Model_Action_Confirm
	 * @throws Mage_Core_Exception
	 */
	protected function checkPaymentAmount() {
		df_assert(
				$this->getRequestValuePaymentAmount()->getAsInteger()
			===
				$this->getPaymentAmountFromOrder()->getAsInteger()
			,rm_sprintf(
				$this->getMessage(self::CONFIG_KEY__MESSAGE__INVALID__PAYMENT_AMOUNT)
				,$this->getPaymentAmountFromOrder()->getAsInteger()
				,$this->getServiceConfig()->getCurrencyCode()
				,$this->getRequestValuePaymentAmount()->getAsInteger()
				,$this->getServiceConfig()->getCurrencyCode()
			)
		);
		return $this;
	}

	/**
	 * Использовать getConst нельзя из-за рекурсии.
	 * @override
	 * @return string
	 */
	protected function getRequestKeyOrderIncrementId() {
		return 'site_order_id';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getSignatureFromOwnCalculations() {
		/** @var array $signatureParams */
		$signatureParams =
			array(
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
			)
		;
		return md5(implode($signatureParams));
	}

	/**
	 * @override
	 * @return bool
	 */
	protected function needInvoice() {
		return
			in_array(
				$this->getRequestValueServicePaymentState()
				,array(self::PAYMENT_STATE__AUTHORIZED, self::PAYMENT_STATE__COMPLETED)
			)
		;
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
		 * @link https://mail.google.com/mail/u/0/?ui=2&ik=a7a1e9bc54&view=att&th=135800f28b66a0b2&attid=0.0&disp=inline&safe=1&zw
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
		 * @link https://mail.google.com/mail/u/0/?ui=2&ik=a7a1e9bc54&view=att&th=135800f28b66a0b2&attid=0.0&disp=inline&safe=1&zw
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
			df_a(
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

	const _CLASS = __CLASS__;
	const PAYMENT_STATE__COMPLETED = 1;
	const PAYMENT_STATE__DECLINED = 2;
	const PAYMENT_STATE__PENDING = 3;
	const PAYMENT_STATE__AUTHORIZED = 4;
	const PAYMENT_STATE__REFUNDED = 5;
	const PAYMENT_STATE__SYSTEM = 6;
	const PAYMENT_STATE__VOIDED = 7;
	/**
	 * @static
	 * @param Df_WebPay_ConfirmController $controller
	 * @return Df_WebPay_Model_Action_Confirm
	 */
	public static function i(Df_WebPay_ConfirmController $controller) {
		return new self(array(self::P__CONTROLLER => $controller));
	}
}