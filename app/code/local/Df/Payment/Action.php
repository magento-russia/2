<?php
namespace Df\Payment;
use Df_Sales_Model_Order as O;
use Mage_Sales_Model_Order_Invoice as Invoice;
use Mage_Sales_Model_Order_Payment as OP;
abstract class Action extends \Df_Core_Model_Action {
	/**
	 * @abstract
	 * @used-by comment()
	 * @used-by method()
	 * @used-by payment()
	 * @return O
	 */
	abstract protected function order();

	/**
	 * @used-by \Df\Interkassa\Action\Confirm::alternativeProcessWithoutInvoicing()
	 * @used-by Df_LiqPay_Action_Confirm::alternativeProcessWithoutInvoicing()
	 * @used-by Df_OnPay_Action_Confirm::alternativeProcessWithoutInvoicing()
	 * @used-by \Df\Payment\Action::_process()
	 * @used-by \Df\Payment\Action\Confirm::logExceptionToOrderHistory()
	 * @used-by Df_Qiwi_Action_Confirm::alternativeProcessWithoutInvoicing()
	 * @param string $comment
	 * @param bool $isCustomerNotified [optional]
	 * @return void
	 */
	protected function comment($comment, $isCustomerNotified = false) {
		$this->order()->comment($comment, $isCustomerNotified);
	}

	/** @return \Df\Payment\Config\Area\Service */
	protected function configS() {return $this->method()->configS();}

	/**
	 * @param string $configKey
	 * @param bool $isRequired [optional]
	 * @param string $default [optional]
	 * @return string
	 */
	protected function const_($configKey, $isRequired = true, $default = '') {
		/** @var string $key */
		$key = 'request/confirmation/' . $configKey;
		/** @var string $result */
		$result = $this->method()->const_($key, $canBeTest = false);
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
	 * @used-by Df_Psbank_Action_CustomerReturn::getResponseByTransactionType()
	 * @return \Mage_Payment_Model_Info
	 */
	protected function ii() {return $this->method()->getInfoInstance();}

	/**
	 * @used-by getConst()
	 * @used-by info()
	 * @return \Df\Payment\Method\WithRedirect
	 */
	protected function method() {
		if (!isset($this->{__METHOD__})) {
			/** @var \Df\Payment\Method\WithRedirect $result */
			$result = $this->payment()->getMethodInstance();
			if (!$result instanceof \Df\Payment\Method\WithRedirect) {
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
	 * @see \Df\Payment\Request::getPayment()
	 * @return OP
	 */
	protected function payment() {
		if (!isset($this->{__METHOD__})) {
			/** @var OP|bool $result */
			$result = $this->order()->getPayment();
			/**
			 * Не используем тут @see df_assert(), потому что эта функция может быть отключена,
			 * а нам важно показать правильное диагностическое сообщение,
			 * а не «Call to a member function getMethodInstance() on a non-object»
			 */
			if (!$result instanceof OP) {
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
	 * @used-by \Df\Alfabank\Action\CustomerReturn::_process()
	 * @used-by \Df\Avangard\Action\CustomerReturn::_process()
	 * @used-by \Df\IPay\Action\Confirm::_process()
	 * @used-by \Df\Payment\Action\Confirm::_process()
	 * @used-by Df_YandexMoney_Action_CustomerReturn::_process()
	 * @param Invoice $invoice
	 * @return void
	 */
	protected function saveInvoice(Invoice $invoice) {
		/** @var \Df_Core_Model_Resource_Transaction $transaction */
		$transaction = \Df_Core_Model_Resource_Transaction::i();
		$transaction
			->addObject($invoice)
			->addObject($invoice->getOrder())
			->save()
		;
	}
}