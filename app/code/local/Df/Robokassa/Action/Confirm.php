<?php
class Df_Robokassa_Action_Confirm extends Df_Payment_Model_Action_Confirm {
	/**
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
	protected function responseTextForSuccess() {return 'OK' . $this->rOII();}

	/**
	 * 3 октября 2012 года заметил, что Робокасса стала передавать размер заказа
	 * с 6 знаками после запятой вместо 2.
	 * Например, даже если в платёжном запросе написано «0.01»,
	 * Робокасса в подтверждении указывает размер заказа как «0.010000».
	 * Поэтому для подписи надо использовать размер заказа в том формате,
	 * как её передает Робокасса («0.010000»)
	 *
	 * 2016-10-21
	 * На сегодня описанное выше поведение системы не замечаю:
	 * "OutSum": "14050.00"
	 * "IncSum": "15033.50"
	 * Хотя в любом случае вывод 2012 года верен:
	 * для подписи надо использовать знпчение в формате Робокассы.
	 *
	 * @override
	 * @return string
	 */
	protected function signatureOwn() {return md5(implode(':', [
		$this->rAmountS(), $this->rOII(), $this->getResponsePassword()
	]));}
}