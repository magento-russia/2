<?php
class Df_NightExpress_Model_Carrier extends Df_Shipping_Model_Carrier {
	/**
	 * @override
	 * @return string
	 */
	public function getRmId() {return 'night-express';}
	/**
	 * @override
	 * @return bool
	 */
	public function isTrackingAvailable() {return true;}
}