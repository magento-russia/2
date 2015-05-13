<?php
class Df_YandexMoney_Model_Payment extends Df_Payment_Model_Method_WithRedirect {
	/**
	 * Результат метода говорит системе о том, поддерживает ли способ оплаты
	 * автоматизированный возврат оплаты покупателю
	 * @override
	 * @return bool
	 */
	public function canRefund() {return true;}
	/**
	 * Результат метода говорит системе о том, поддерживает ли способ оплаты
	 * автоматизированный возврат части оплаты покупателю.
	 * Если способ оплаты частичный возврат допускает или же вообще возврата не допускает,
	 * то на странице документа-возврата появляется возможность редактирования
	 * количества возвращаемого товара.
	 * @see Mage_Adminhtml_Block_Sales_Order_Creditmemo_Create_Items::canEditQty():
		public function canEditQty() {
		 if ($this->getCreditmemo()->getOrder()->getPayment()->canRefund()) {
			 return $this->getCreditmemo()->getOrder()->getPayment()->canRefundPartialPerInvoice();
		 }
		 return true;
	 }
	 * @override
	 * @return bool
	 */
	public function canRefundPartialPerInvoice() {return true;}
	/**
	 * Результат метода говорит системе о том, поддерживает ли способ оплаты
	 * автоматизированное разблокирование (возврат покупателю)
	 * ранее зарезервированных (но не снятых со счёта покупателя) средств
	 * @override
	 * @param Varien_Object $payment
	 * @return bool
	 */
	public function canVoid(Varien_Object $payment) {return true;}
	/**
	 * @override
	 * @return string
	 */
	public function getRmId() {return 'yandex-money';}

	/** @return Df_YandexMoney_Model_Payment */
	public static function i() {return new self;}
}