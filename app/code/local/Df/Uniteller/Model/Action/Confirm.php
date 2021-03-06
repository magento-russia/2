<?php
class Df_Uniteller_Model_Action_Confirm extends Df_Payment_Model_Action_Confirm {
	/**
	 * @override
	 * @return Df_Uniteller_Model_Action_Confirm
	 */
	protected function alternativeProcessWithoutInvoicing() {
		parent::alternativeProcessWithoutInvoicing();
		$this->getOrder()
			->addStatusHistoryComment(
				$this->getPaymentStateMessage(
					$this->getRequestValueServicePaymentState()
				)
			)
		;
		$this->getOrder()->setData(Df_Sales_Const::ORDER_PARAM__IS_CUSTOMER_NOTIFIED, false);
		$this->getOrder()->save();
		return $this;
	}

	/**
	 * @override
	 * @return Df_Uniteller_Model_Action_Confirm
	 * @throws Mage_Core_Exception
	 */
	protected function checkPaymentAmount() {
		return $this;
	}

	/**
	 * Использовать getConst нельзя из-за рекурсии.
	 * @override
	 * @return string
	 */
	protected function getRequestKeyOrderIncrementId() {
		return 'Order_ID';
	}

	/**
	 * После успешной оплаты картой Покупателя
	 * система Uniteller уведомляет сервер интернет-магазина Мёрчанта
	 * об изменении статуса заказа на authorized.
	 * Если уведомление по каким-либо причинам не получено сервером интернет-магазина
	 * (например, в момент уведомления сервер недоступен),
	 * то система Uniteller сделает дополнительные попытки посылки уведомления.
	 * Всего (вместе с первым уведомлением, закончившимся неудачей)
	 * будет сделано 10 попыток уведомления сервера интернет-магазина
	 * за период около 6,5 мин. или больше
	 * (реальные интервалы между уведомлениями интернет-магазина
	 * зависят от загрузки системы Uniteller и размера очереди операций).
	 * В случае, если все 10 попыток уведомить сервер интернет-магазина
	 * об изменении статуса оплаты не будут приняты,
	 * дальнейшие попытки прекращаются,
	 * а интернет-магазину для уточнения статуса интересующей оплаты
	 * следует самостоятельно инициировать запрос,
	 * как описано в п. 6.7.1 «Запрос результатов авторизации» на стр. 40.
	 *
	 * @link https://mail.google.com/mail/u/0/?ui=2&ik=a7a1e9bc54&view=att&th=1344267973cfe8ac&attid=0.1&disp=inline&safe=1&zw
	 */

	/**
	 * @override
	 * @return string
	 */
	protected function getSignatureFromOwnCalculations() {
		/** @var array $signatureParams */
		$signatureParams =
			array(
				$this->getRequestValueOrderIncrementId()
				,$this->getRequestValueServicePaymentState()
				,$this->getResponsePassword()
			)
		;
		return strtoupper(md5(implode($signatureParams)));
	}

	/**
	 * @override
	 * @return bool
	 */
	protected function needInvoice() {
		/**
		 * Дело в том, что, как я понял,
		 * в случае оплаты покупателем заказа электронной валютой,
		 * платёжная система сразу отсылает статус «paid»,
		 * а в случае оплаты банковской картой —
		 * сначала «authorized», и лишь потом — «paid».
		 */
		return
				$this->getOrder()->canInvoice()
			&&
				in_array(
					$this->getRequestValueServicePaymentState()
					,array(self::PAYMENT_STATE__AUTHORIZED, self::PAYMENT_STATE__PAID)
				)
		;
	}

	/**
	 * @param string $code
	 * @return string
	 */
	private function getPaymentStateMessage($code) {
		df_param_string($code, 0);
		/** @var string $result */
		$result =
			df_a(
				array(
					self::PAYMENT_STATE__AUTHORIZED => 'Cредства на карте покупателя заблокированы'
					,self::PAYMENT_STATE__PAID => 'Оплата получена'
					,self::PAYMENT_STATE__CANCELED => 'Покупатель отказался от оплаты'
				)
				,$code
			)
		;
		df_result_string($result);
		return $result;
	}

	const _CLASS = __CLASS__;
	const PAYMENT_STATE__AUTHORIZED = 'authorized';
	const PAYMENT_STATE__PAID = 'paid';
	const PAYMENT_STATE__CANCELED = 'canceled';
	/**
	 * @static
	 * @param Df_Uniteller_ConfirmController $controller
	 * @return Df_Uniteller_Model_Action_Confirm
	 */
	public static function i(Df_Uniteller_ConfirmController $controller) {
		return new self(array(self::P__CONTROLLER => $controller));
	}
}