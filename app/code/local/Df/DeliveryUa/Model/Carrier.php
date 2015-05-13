<?php
class Df_DeliveryUa_Model_Carrier extends Df_Shipping_Model_Carrier {
	/**
	 * @override
	 * @return string
	 */
	public function getRmId() {return 'delivery-ua';}
	/**
	 * @override
	 * @return bool
	 */
	public function isTrackingAvailable() {return true;}
}