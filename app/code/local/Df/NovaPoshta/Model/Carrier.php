<?php
class Df_NovaPoshta_Model_Carrier extends Df_Shipping_Model_Carrier {
	/**
	 * @override
	 * @return string
	 */
	public function getRmId() {return 'nova-poshta';}
}