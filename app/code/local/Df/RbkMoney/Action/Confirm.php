<?php
class Df_RbkMoney_Action_Confirm extends Df_Payment_Action_Confirm {
	/**
	 * Использовать getConst нельзя из-за рекурсии.
	 * @override
	 * @return string
	 */
	protected function rkOII() {return 'orderId';}

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
	protected function responseTextForError(Exception $e) {return 'OK';}

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
		/** @var mixed[] $signatureParams */
		$signatureParams = array(
			$this->rShopId()
			,$this->rOII()
			// 2016-10-21
			// Назначение платежа в соответствии с системой учета продавца.
			,$this->param('serviceName')
			// 2016-10-21
			// Номер кошелька магазина в системе RBK Money.
			,$this->param('eshopAccount')
			,$this->rAmountS()
			,$this->rCurrencyC()
			,$this->rState()
			,$this->param('userName')
			,$this->param('userEmail')
			,$this->rTime()
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
		return self::PAYMENT_STATE__PROCESSING !== df_int($this->rState());
	}

	const PAYMENT_STATE__PROCESSED = 5;
	const PAYMENT_STATE__PROCESSING = 3;
	const SIGNATURE_SEPARATOR = '::';
	const T__PAYMENT_STATE__PROCESSING = 'Покупатель находится на сайте RBK Money';
}