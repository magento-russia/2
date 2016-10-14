<?php
class Df_WebMoney_Model_Action_Confirm extends Df_Payment_Model_Action_Confirm {
	/**
	 * @override
	 * @return void
	 */
	protected function alternativeProcessWithoutInvoicing() {
		$this->addAndSaveStatusHistoryComment(
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
	protected function getRequestKeyOrderIncrementId() {return 'LMI_PAYMENT_NO';}

	/**
	 * @override
	 * @return string
	 */
	protected function getSignatureFromOwnCalculations() {
		/** @var array $signatureParams */
		$signatureParams = array(
			$this->getRequestValueShopId()
			,$this->getRequestValuePaymentAmountAsString()
			,$this->getRequestValueOrderIncrementId()
			,$this->getRequestValueServicePaymentTest()
			,$this->getRequest()->getParam('LMI_SYS_INVS_NO')
			,$this->getRequestValueServicePaymentId()
			,$this->getRequestValueServicePaymentDate()
			,$this->getResponsePassword()
			,$this->getRequestValueServiceCustomerAccountId()
			,$this->getRequestValueServiceCustomerId()
		);
		return md5(implode($signatureParams));
	}

	/**
	 * @override
	 * @return bool
	 */
	protected function needInvoice() {return !rm_bool($this->getRequest()->getParam('LMI_PREREQUEST'));}

	/**
	 * @override
	 * @return void
	 */
	protected function processPrepare() {
		parent::processPrepare();
		if (!$this->getRequest()->getParams()) {
			df_error(
				"Платёжная система WebMoney прислала подтверждение оплаты безо всяких параметров.
				\nВидимо, администратор магазина некачественно настроил Личный кабинет WebMoney:
				забыл включить опцию «Передавать параметры в предварительном запросе»."
			);
		}
	}

	/**
	 * Кошелек покупателя
	 * @return string
	 */
	private function getRequestKeyServiceCustomerAccountId() {
		return $this->getConst(self::CONFIG_KEY__PAYMENT_SERVICE__CUSTOMER__ACCOUNT_ID);
	}

	/**
	 * WMId покупателя
	 * @return string
	 */
	private function getRequestKeyServiceCustomerId() {
		return $this->getConst(self::CONFIG_KEY__PAYMENT_SERVICE__CUSTOMER__ID);
	}

	/**
	 * Указывает, в каком режиме выполнялась обработка запроса на платеж.
	 * Может принимать два значения:
	 * 	0:	Платеж выполнялся в реальном режиме,
	 * 		средства переведены с кошелька покупателя
	 * 		на кошелек продавца;
	 * 	1:	Платеж выполнялся в тестовом режиме,
	 * 		средства реально не переводились.
	 * @return string
	 */
	private function getRequestKeyServicePaymentTest() {
		return $this->getConst(self::CONFIG_KEY__PAYMENT_SERVICE__PAYMENT__TEST);
	}

	/**
	 * Кошелек покупателя
	 * @return string
	 */
	private function getRequestValueServiceCustomerAccountId() {
		/** @var string $result */
		$result = $this->getRequest()->getParam($this->getRequestKeyServiceCustomerAccountId());
		df_result_string($result);
		return $result;
	}

	/**
	 * WMId покупателя
	 * @return string
	 */
	private function getRequestValueServiceCustomerId() {
		/** @var string $result */
		$result = $this->getRequest()->getParam($this->getRequestKeyServiceCustomerId());
		df_result_string($result);
		return $result;
	}

	/**
	 * Указывает, в каком режиме выполнялась обработка запроса на платеж.
	 * Может принимать два значения:
	 * 	0:	Платеж выполнялся в реальном режиме,
	 * 		средства переведены с кошелька покупателя
	 * 		на кошелек продавца;
	 * 	1:	Платеж выполнялся в тестовом режиме,
	 * 		средства реально не переводились.
	 * @return string
	 */
	private function getRequestValueServicePaymentTest() {
		/** @var string $result */
		$result = $this->getRequest()->getParam($this->getRequestKeyServicePaymentTest());
		df_result_string($result);
		return $result;
	}

	const _C = __CLASS__;
	/**
	 * Кошелек покупателя
	 */
	const CONFIG_KEY__PAYMENT_SERVICE__CUSTOMER__ACCOUNT_ID = 'payment_service/customer/account-id';

	/**
	 * WMId покупателя
	 */
	const CONFIG_KEY__PAYMENT_SERVICE__CUSTOMER__ID = 'payment_service/customer/id';
	/**
	 * Указывает, в каком режиме выполнялась обработка запроса на платеж.
	 * Может принимать два значения:
	 * 	0:	Платеж выполнялся в реальном режиме,
	 * 		средства переведены с кошелька покупателя
	 * 		на кошелек продавца;
	 * 	1:	Платеж выполнялся в тестовом режиме,
	 * 		средства реально не переводились.
	 */
	const CONFIG_KEY__PAYMENT_SERVICE__PAYMENT__TEST = 'payment_service/payment/test';
}