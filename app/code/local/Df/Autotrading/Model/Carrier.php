<?php
class Df_Autotrading_Model_Carrier extends Df_Shipping_Model_Carrier {
	/**
	 * @override
	 * @return bool
	 */
	public function hasTheOnlyMethod() {return true;}
	/**
	 * @override
	 * @return bool
	 */
	public function isTrackingAvailable() {return true;}
}