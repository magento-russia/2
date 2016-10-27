<?php
namespace Df\Avangard\Request;
use Mage_Sales_Model_Order_Payment as OP;
class State extends Secondary {
	/**
	 * @override
	 * @return string
	 */
	protected function getGenericFailureMessageUniquePart() {
		return 'запросе состояния заказа в системе Банка Авангард';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getRequestId() {return 'get_order_info';}

	/**
	 * @param OP $payment
	 * @return self
	 */
	public static function i(OP $payment) {return self::ic(__CLASS__, $payment);}
}