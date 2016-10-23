<?php
class Df_Alfabank_Request_State extends Df_Alfabank_Request_Secondary {
	/**
	 * @override
	 * @return string
	 */
	protected function getGenericFailureMessageUniquePart() {
		return 'запросе состояния заказа в системе Альфа-Банка';
	}

	/**
	 * @override
	 * @used-by Df_Alfabank_Request_Secondary::getUri()
	 * @return string
	 */
	protected function getServiceName() {return 'getOrderStatus';}

	/**
	 * @param Mage_Sales_Model_Order_Payment $payment
	 * @return Df_Alfabank_Request_State
	 */
	public static function i(Mage_Sales_Model_Order_Payment $payment) {return self::ic(__CLASS__, $payment);}
}


