<?php
class Df_Robokassa_Action_Confirm extends Df_Payment_Model_Action_Confirm {
	/**
	 * Использовать getConst нельзя из-за рекурсии.
	 * @override
	 * @return string
	 */
	protected function rkOII() {return 'InvId';}

	/**
	 * @override
	 * @param Exception $e
	 * @return string
	 */
	protected function responseTextForError(Exception $e) {return df_ets($e);}

	/**
	 * @override
	 * @return string
	 */
	protected function responseTextForSuccess() {
		return self::RESPONSE_TEXT__SUCCESS__PREFIX . $this->rOII();
	}

	/**
	 * @override
	 * @return string
	 */
	protected function signatureOwn() {
		/** @var string $result */
		$result = md5(implode(':', array(
			/**
			 * 3 октября 2012 года заметил, что Робокасса стала передавать размер заказа
			 * с 6 знаками после запятой вместо 2.
			 * Например, даже если в платёжном запросе написано «0.01»,
			 * Робокасса в подтверждении указывает размер заказа как «0.010000».
			 * Поэтому для подписи надо использовать размер заказа в том формате,
			 * как её передает Робокасса («0.010000»)
			 */
			$this->rAmountS()
			,$this->rOII()
			,$this->getResponsePassword()
		)));
		return $result;
	}

	const RESPONSE_TEXT__SUCCESS__PREFIX = 'OK';
}