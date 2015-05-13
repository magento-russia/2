<?php
class Df_Moneta_Model_Action_Confirm extends Df_Payment_Model_Action_Confirm {
	/**
	 * Использовать getConst нельзя из-за рекурсии.
	 * @override
	 * @return string
	 */
	protected function getRequestKeyOrderIncrementId() {
		return 'MNT_TRANSACTION_ID';
	}

	/**
	 * @override
	 * @param Exception $e
	 * @return string
	 */
	protected function getResponseTextForError(Exception $e) {
		/**
		 * Если система «MONETA.RU» не смогла получить ответ от обработчика,
		 * либо сервер был недоступен, либо текстовая строка начинается словом «FAIL»,
		 * то уведомление считается не доставленным.
		 * Попытки отправки уведомления будут повторены.
		 * @link https://moneta.ru/doc/MONETA.Assistant.ru.pdf
		 *
		 * Обратите внимание, что даже в случае сбоя нам выгодно отвечать 'SUCCESS',
		 * потому что всё равно мы в реальном времени причину сбоя не устраним,
		 * а вот «MONETA.RU» нас повторными запросами задолбает.
		 */
		return 'SUCCESS';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getResponseTextForSuccess() {
		/**
		 * О результате приема отчета об оплате
		 * магазину необходимо в обработчике адреса «Pay URL»
		 * вернуть в качестве ответа текстовую строку в формате UTF-8.
		 * Если текстовая строка начинается словом «SUCCESS»,
		 * то отчет считается принятым и операция благополучно завершается.
		 * Ответ об успешном получении уведомления следует возвращать также в том случае,
		 * если учетной системой магазина уведомление принято повторно,
		 * то есть, в том случае, когда магазин уже отвечал
		 * результатом «SUCCESS» на предшествующие уведомления.
		 * @link https://moneta.ru/doc/MONETA.Assistant.ru.pdf
		 */
		return 'SUCCESS';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getSignatureFromOwnCalculations() {
		/** @var string[] $signatureParams */
		$signatureParams =
			array(
				$this->getRequestValueShopId()
				,$this->getRequestValueOrderIncrementId()
				,$this->getRequestValueServicePaymentId()
				,$this->getRequestValuePaymentAmountAsString()
				,$this->getRequestValuePaymentCurrencyCode()
				,$this->getRequestValuePaymentTest()
				,$this->getResponsePassword()
			)
		;
		return md5(implode($signatureParams));
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param Df_Moneta_ConfirmController $controller
	 * @return Df_Moneta_Model_Action_Confirm
	 */
	public static function i(Df_Moneta_ConfirmController $controller) {
		return new self(array(self::P__CONTROLLER => $controller));
	}
}