<?php
class Df_Avangard_Request_State extends Df_Avangard_Request_Secondary {
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
	 * @param Mage_Sales_Model_Order_Payment $payment
	 * @return Df_Avangard_Request_State
	 */
	public static function i(Mage_Sales_Model_Order_Payment $payment) {return self::ic(__CLASS__, $payment);}
}