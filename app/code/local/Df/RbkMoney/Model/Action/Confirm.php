<?php
class Df_RbkMoney_Model_Action_Confirm extends Df_Payment_Model_Action_Confirm {
	/**
	 * Использовать getConst нельзя из-за рекурсии.
	 * @override
	 * @return string
	 */
	protected function getRequestKeyOrderIncrementId() {return 'orderId';}

	/**
	 * Даже в случае сбоя отсылаем код успешного завершения операции,
	 * иначе RBK Money замучает нас повторными запросами.
	 *
	 * При выполнении перевода или возврата
	 * система RBK Money высылает оповещение используя сервис оповещения о платеже (callback)
	 * на URL оповещения о платеже, указанный участником в настройках магазина.
	 *
	 * Если используется версия протокола по умолчанию (version=1),
	 * то сообщение считается доставленным,
	 * если страница обрабатывающая уведомление,
	 * вернет код состояния HTTP 200 (ОК).
	 * В случае ошибки при уведомлении,
	 * оповещение будет послано повторно через 3 минуты,
	 * будет предпринято 5 попыток доставить сообщение.
	 *
	 * Если используется версия 2 (version=2),
	 * то в ответ на оповещение о Переводе Вы должны вернуть 'OK',
	 * иначе оповещение будет послано повторно через 3 минуты,
	 * и будет предпринято 480 попыток доставить сообщение.
	 *
	 * http://www.rbkmoney.ru/sites/default/files/doc/rbkmoney_api.pdf
	 * страница 34
	 *
	 * @override
	 * @param Exception $e
	 * @return string
	 */
	protected function getResponseTextForError(Exception $e) {return 'OK';}

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
		/** @var mixed[] $signatureParams */
		$signatureParams = array(
			$this->getRequestValueShopId()
			,$this->getRequestValueOrderIncrementId()
			,$this->getRequestValuePaymentDescription()
			,$this->getRequestValueShopAccountId()
			,$this->getRequestValuePaymentAmountAsString()
			,$this->getRequestValuePaymentCurrencyCode()
			,$this->getRequestValueServicePaymentState()
			,$this->getRequestValueCustomerName()
			,$this->getRequestValueCustomerEmail()
			,$this->getRequestValueServicePaymentDate()
			,$this->getResponsePassword()
		);
		/** @var string $result */
		$result = md5(implode(self::SIGNATURE_SEPARATOR, $signatureParams));
		return $result;
	}

	/**
	 * @override
	 * @return bool
	 */
	protected function needInvoice() {
		return self::PAYMENT_STATE__PROCESSING !== df_int($this->getRequestValueServicePaymentState());
	}

	/** @return string */
	private function getRequestKeyPaymentDescription() {
		return $this->getConst(self::CONFIG_KEY__PAYMENT__DESCRIPTION);
	}

	/** @return string */
	private function getRequestKeyShopAccountId() {
		return $this->getConst(self::CONFIG_KEY__SHOP__ACCOUNT_ID);
	}

	/** @return string */
	private function getRequestValuePaymentDescription() {
		/** @var string $result */
		$result = $this->getRequest()->getParam($this->getRequestKeyPaymentDescription());
		df_result_string($result);
		return $result;
	}

	/** @return string */
	private function getRequestValueShopAccountId() {
		/** @var string $result */
		$result = $this->getRequest()->getParam($this->getRequestKeyShopAccountId());
		df_result_string($result);
		return $result;
	}

	const CONFIG_KEY__PAYMENT__DESCRIPTION = 'payment/description';
	const CONFIG_KEY__SHOP__ACCOUNT_ID = 'shop/account-id';
	const PAYMENT_STATE__PROCESSED = 5;
	const PAYMENT_STATE__PROCESSING = 3;
	const SIGNATURE_SEPARATOR = '::';
	const T__PAYMENT_STATE__PROCESSING = 'Покупатель находится на сайте RBK Money';
}