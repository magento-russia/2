<?php
class Df_Megapolis_Model_Carrier extends Df_Shipping_Carrier {
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