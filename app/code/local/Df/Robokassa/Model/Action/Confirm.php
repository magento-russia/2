<?php
class Df_Robokassa_Model_Action_Confirm extends Df_Payment_Model_Action_Confirm {
	/**
	 * Использовать getConst нельзя из-за рекурсии.
	 * @override
	 * @return string
	 */
	protected function getRequestKeyOrderIncrementId() {return 'InvId';}

	/**
	 * @override
	 * @param Exception $e
	 * @return string
	 */
	protected function getResponseTextForError(Exception $e) {return df_ets($e);}

	/**
	 * @override
	 * @return string
	 */
	protected function getResponseTextForSuccess() {
		return self::RESPONSE_TEXT__SUCCESS__PREFIX . $this->getRequestValueOrderIncrementId();
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getSignatureFromOwnCalculations() {
		/** @var string $result */
		$result = md5(implode(self::SIGNATURE_PARTS_SEPARATOR, array(
			/**
			 * 3 октября 2012 года заметил, что Робокасса стала передавать размер заказа
			 * с 6 знаками после запятой вместо 2.
			 * Например, даже если в платёжном запросе написано «0.01»,
			 * Робокасса в подтверждении указывает размер заказа как «0.010000».
			 * Поэтому для подписи надо использовать размер заказа в том формате,
			 * как её передает Робокасса («0.010000»)
			 */
			$this->getRequestValuePaymentAmountAsString()
			,$this->getRequestValueOrderIncrementId()
			,$this->getResponsePassword()
		)));
		return $result;
	}

	const RESPONSE_TEXT__SUCCESS__PREFIX = 'OK';
	const SIGNATURE_PARTS_SEPARATOR = Df_Robokassa_Model_Request_Payment::SIGNATURE_PARTS_SEPARATOR;
}