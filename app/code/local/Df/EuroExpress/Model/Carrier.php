<?php
class Df_EuroExpress_Model_Carrier extends Df_Shipping_Model_Carrier {
	/**
	 * @override
	 * @return string
	 */
	public function getRmId() {return 'euro-express';}
	/**
	 * @override
	 * @return bool
	 */
	public function isTrackingAvailable() {return true;}
}