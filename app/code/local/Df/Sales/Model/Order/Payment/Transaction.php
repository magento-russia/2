<?php
class Df_Sales_Model_Order_Payment_Transaction extends Mage_Sales_Model_Order_Payment_Transaction {
	/**
	 * Цели перекрытия:
	 * 1) перевод названий типов транзакций
	 * 2) удаление типа транзакции @see Mage_Sales_Model_Order_Payment_Transaction::TYPE_ORDER
	 * (потому что этот тип встречается только в PayPal Express Checkout)
	 * @override
	 * @return array(string => string)
	 */
	public function getTransactionTypes() {
		return array(
			// Помимо перевода, удалил из массива self::TYPE_ORDER,
			// потому что этот тип встречается только в PayPal Express Checkout
			self::TYPE_CAPTURE => 'приём оплаты'
			,self::TYPE_AUTH => 'блокирование средств'
			,self::TYPE_VOID => 'разблокирование средств'
			,self::TYPE_REFUND => 'возврат оплаты'
		);
	}
}