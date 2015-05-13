<?php
/**
 * @method Df_Alfabank_Model_Request_Payment getRequestPayment()
 */
class Df_Alfabank_Model_Payment extends Df_Payment_Model_Method_WithRedirect {
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
	 * Обратите внимание, что платёжный шлюз Альфа-Банка (@see Df_Alfabank_Model_Payment)
	 * не нуждается в получении параметров при перенаправлении на него покупателя.
	 * Вместо этого модуль Альфа-Банк передаёт эти параметры предварительным запросом,
	 * и платёжный шлюз возвращает модулю уникальный веб-адрес,
	 * на который модуль перенаправляет покупателя без параметров.
	 *
	 * Если в других модулях потребуется такое же поведение (перенаправление без параметров),
	 * то посмотрите, как устроен модуль Альфа-Банк:
	 * он перекрывает метод @see Df_Payment_Model_Method_WithRedirect::getPaymentPageParams()
	 * (@see Df_Alfabank_Model_Payment::getPaymentPageParams()),
	 * а класс @see Df_Alfabank_Model_Request_Payment используется
	 * не для получения параметров перенаправления покупателя на платёжный шлюз,
	 * а для предварительной регистрации заказа в платёжном шлюзе.
	 * @override
	 * @return array(string => mixed)
	 */
	public function getPaymentPageParams() {return array();}
	/**
	 * @override
	 * @return string
	 */
	public function getPaymentPageUrl() {return $this->getRequestPayment()->getPaymentPageUrl();}
	const INFO__PAYMENT_EXTERNAL_ID = 'order_external_id';
}