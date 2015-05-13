<?php
class Df_Pec_Model_Carrier extends Df_Shipping_Model_Carrier {
	/**
	 * @override
	 * @return bool
	 */
	public function isTrackingAvailable() {
		return true;
	}

	const _CLASS = __CLASS__;
}