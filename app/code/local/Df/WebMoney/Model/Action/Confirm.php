<?php
class Df_WebMoney_Model_Action_Confirm extends Df_Payment_Model_Action_Confirm {
	/**
	 * @override
	 * @return Df_WebMoney_Model_Action_Confirm
	 */
	protected function alternativeProcessWithoutInvoicing() {
		parent::alternativeProcessWithoutInvoicing();
		$this->getOrder()
			->addStatusHistoryComment(
				'Предварительная проверка платёжной системой работоспособности магазина'
			)
		;
		$this->getOrder()->setData(Df_Sales_Const::ORDER_PARAM__IS_CUSTOMER_NOTIFIED, false);
		$this->getOrder()->save();
		return $this;
	}

	/**
	 * @override
	 * @return Df_WebMoney_Model_Action_Confirm
	 */
	public function process() {
		try {
			if (0 === count($this->getRequest()->getParams())) {
				df_error(
					"Платёжная система WebMoney прислала подтверждение оплаты безо всяких параметров.
					\nВидимо, администратор магазина некачественно настроил Личный кабинет WebMoney:
					забыл включить опцию «Передавать параметры в предварительном запросе»."
				);
			}
			parent::process();
		}
		catch(Exception $e) {
			$this->processException($e);
		}
		return $this;
	}

	/**
	 * @override
	 * @return Df_Payment_Model_Action_Confirm
	 * @throws Mage_Core_Exception
	 */
	protected function checkSignature() {
		if ($this->needInvoice()) {
			parent::checkSignature();
		}
		return $this;
	}

	/**
	 * Использовать getConst нельзя из-за рекурсии.
	 * @override
	 * @return string
	 */
	protected function getRequestKeyOrderIncrementId() {
		return 'LMI_PAYMENT_NO';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getSignatureFromOwnCalculations() {
		/** @var array $signatureParams */
		$signatureParams =
			array(
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
			)
		;
		return md5(implode($signatureParams));
	}

	/**
	 * @override
	 * @return bool
	 */
	protected function needInvoice() {return !rm_bool($this->getRequest()->getParam('LMI_PREREQUEST'));}

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

	const _CLASS = __CLASS__;
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
	/**
	 * @static
	 * @param Df_WebMoney_ConfirmController $controller
	 * @return Df_WebMoney_Model_Action_Confirm
	 */
	public static function i(Df_WebMoney_ConfirmController $controller) {
		return new self(array(self::P__CONTROLLER => $controller));
	}
}