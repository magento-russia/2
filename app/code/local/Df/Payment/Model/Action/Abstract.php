<?php
abstract class Df_Payment_Model_Action_Abstract extends Df_Core_Model_Action {
	/**
	 * @abstract
	 * @used-by comment()
	 * @used-by method()
	 * @used-by payment()
	 * @return Df_Sales_Model_Order
	 */
	abstract protected function order();

	/**
	 * @used-by Df_Interkassa_Model_Action_Confirm::alternativeProcessWithoutInvoicing()
	 * @used-by Df_LiqPay_Model_Action_Confirm::alternativeProcessWithoutInvoicing()
	 * @used-by Df_OnPay_Model_Action_Confirm::alternativeProcessWithoutInvoicing()
	 * @used-by Df_Payment_Model_Action_Abstract::_process()
	 * @used-by Df_Payment_Model_Action_Confirm::logExceptionToOrderHistory()
	 * @used-by Df_Qiwi_Model_Action_Confirm::alternativeProcessWithoutInvoicing()
	 * @param string $comment
	 * @param bool $isCustomerNotified [optional]
	 * @return void
	 */
	protected function comment($comment, $isCustomerNotified = false) {
		$this->order()->comment($comment, $isCustomerNotified);
	}

	/** @return Df_Payment_Config_Area_Service */
	protected function configS() {return $this->method()->configS();}

	/**
	 * @param string $configKey
	 * @param bool $isRequired [optional]
	 * @param string $default [optional]
	 * @return string
	 */
	protected function getConst($configKey, $isRequired = true, $default = '') {
		/** @var string $key */
		$key = 'request/confirmation/' . $configKey;
		/** @var string $result */
		$result = $this->method()->getConst($key, $canBeTest = false);
		if ('' === $result) {
			if ($isRequired) {
				df_error(
					'В файле «config.xml» модуля «%s» отсутствует требуемый ключ «%s».'
					, df_module_name($this->method()), $key
				);
			}
			$result = $default;
		}
		return $result;
	}

	/**
	 * @used-by Df_Psbank_Model_Action_CustomerReturn::getResponseByTransactionType()
	 * @return Mage_Payment_Model_Info
	 */
	protected function info() {return $this->method()->getInfoInstance();}

	/**
	 * @used-by getConst()
	 * @used-by info()
	 * @return Df_Payment_Model_Method_WithRedirect
	 */
	protected function method() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Payment_Model_Method_WithRedirect $result */
			$result = $this->payment()->getMethodInstance();
			if (!$result instanceof Df_Payment_Model_Method_WithRedirect) {
				df_error(
					'Платёжная система прислала сообщение относительно заказа №«%s»,'
					. ' который не предназначен для оплаты последством данной платёжной системы.'
					,$this->order()->getIncrementId()
				);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-03-08
	 * Обратите внимание, что:
	 * 1) @uses Mage_Sales_Model_Order::getPayment() может иногда возвращать false
	 * 2) Результат @uses Mage_Sales_Model_Order::getPayment() разумно кэшировать
	 * в силу реализации этого метода (там используется foreach).
	 * @used-by method()
	 * @see Df_Payment_Model_Request::getPayment()
	 * @return Mage_Sales_Model_Order_Payment
	 */
	protected function payment() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Sales_Model_Order_Payment|bool $result */
			$result = $this->order()->getPayment();
			/**
			 * Не используем тут @see df_assert(), потому что эта функция может быть отключена,
			 * а нам важно показать правильное диагностическое сообщение,
			 * а не «Call to a member function getMethodInstance() on a non-object»
			 */
			if (!$result instanceof Mage_Sales_Model_Order_Payment) {
				df_error(
					'Платёжная система прислала сообщение
					относительно заказа №«%s», который не предназначен для оплаты.'
					,$this->order()->getIncrementId()
				);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by Df_Alfabank_Model_Action_CustomerReturn::_process()
	 * @used-by Df_Avangard_Model_Action_CustomerReturn::_process()
	 * @used-by Df_IPay_Model_Action_Confirm::_process()
	 * @used-by Df_Payment_Model_Action_Confirm::_process()
	 * @used-by Df_YandexMoney_Model_Action_CustomerReturn::_process()
	 * @param Mage_Sales_Model_Order_Invoice $invoice
	 * @return void
	 */
	protected function saveInvoice(Mage_Sales_Model_Order_Invoice $invoice) {
		/** @var Df_Core_Model_Resource_Transaction $transaction */
		$transaction = Df_Core_Model_Resource_Transaction::i();
		$transaction
			->addObject($invoice)
			->addObject($invoice->getOrder())
			->save()
		;
	}
}