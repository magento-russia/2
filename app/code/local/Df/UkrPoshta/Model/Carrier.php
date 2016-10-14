<?php
class Df_UkrPoshta_Model_Carrier extends Df_Shipping_Carrier {
	/**
	 * @override
	 * @return string
	 */
	public function getRmId() {return 'ukr-poshta';}
	/**
	 * @override
	 * @return bool
	 */
	public function isTrackingAvailable() {return true;}
}