<?php
class Df_Sat_Model_Carrier extends Df_Shipping_Model_Carrier {
	/**
	 * @override
	 * @return bool
	 */
	public function isTrackingAvailable() {return true;}
}