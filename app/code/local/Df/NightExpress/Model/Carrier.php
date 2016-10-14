<?php
class Df_NightExpress_Model_Carrier extends Df_Shipping_Carrier {
	/**
	 * @override
	 * @return bool
	 */
	public function isTrackingAvailable() {return true;}
}