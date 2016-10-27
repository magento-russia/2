<?php
namespace Df\Alfabank\Request;
use Mage_Sales_Model_Order_Payment as OP;
class State extends \Df\Alfabank\Request\Secondary {
	/**
	 * @override
	 * @return string
	 */
	protected function getGenericFailureMessageUniquePart() {return
		'запросе состояния заказа в системе Альфа-Банка'
	;}

	/**
	 * @override
	 * @used-by \Df\Alfabank\Request\Secondary::getUri()
	 * @return string
	 */
	protected function getServiceName() {return 'getOrderStatus';}

	/**
	 * @param OP $payment
	 * @return \Df\Alfabank\Request\State
	 */
	public static function i(OP $payment) {return self::ic(__CLASS__, $payment);}
}


