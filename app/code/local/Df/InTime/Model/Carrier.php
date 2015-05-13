<?php
class Df_InTime_Model_Carrier extends Df_Shipping_Model_Carrier {
	/**
	 * @override
	 * @return string
	 */
	public function getRmId() {return 'in-time';}
}