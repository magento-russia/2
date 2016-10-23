<?php
class Df_WebMoney_Action_Confirm extends Df_Payment_Action_Confirm {
	/**
	 * @override
	 * @return void
	 */
	protected function alternativeProcessWithoutInvoicing() {
		$this->order()->comment(
			'Предварительная проверка платёжной системой работоспособности магазина'
		);
	}

	/**
	 * @override
	 * @return void
	 * @throws Mage_Core_Exception
	 */
	protected function checkSignature() {
		if ($this->needInvoice()) {
			parent::checkSignature();
		}
	}

	/**
	 * Использовать getConst нельзя из-за рекурсии.
	 * @override
	 * @return string
	 */
	protected function rkOII() {return 'LMI_PAYMENT_NO';}

	/**
	 * @override
	 * @return string
	 */
	protected function signatureOwn() {
		/** @var array $signatureParams */
		$signatureParams = array(
			$this->rShopId()
			,$this->rAmountS()
			,$this->rOII()
			/**
			 * 2016-10-21
			 * Указывает, в каком режиме выполнялась обработка запроса на платеж.
			 * Может принимать два значения:
			 * 	0:	Платеж выполнялся в реальном режиме,
			 * 		средства переведены с кошелька покупателя
			 * 		на кошелек продавца;
			 * 	1:	Платеж выполнялся в тестовом режиме,
			 * 		средства реально не переводились.
			 */
			,$this->param('LMI_MODE')
			,$this->param('LMI_SYS_INVS_NO')
			,$this->rExternalId()
			,$this->rTime()
			,$this->getResponsePassword()
			// 2016-10-21
			// Кошелек покупателя
			,$this->param('LMI_PAYER_PURSE')
			// 2016-10-21
			// WMId покупателя
			,$this->param('LMI_PAYER_WM')
		);
		return md5(implode($signatureParams));
	}

	/**
	 * @override
	 * @return bool
	 */
	protected function needInvoice() {return !df_bool($this->param('LMI_PREREQUEST'));}

	/**
	 * @override
	 * @return void
	 */
	protected function processPrepare() {
		parent::processPrepare();
		if (!$this->params()) {
			df_error(
				"Платёжная система WebMoney прислала подтверждение оплаты безо всяких параметров.
				\nВидимо, администратор магазина некачественно настроил Личный кабинет WebMoney:
				забыл включить опцию «Передавать параметры в предварительном запросе»."
			);
		}
	}
}