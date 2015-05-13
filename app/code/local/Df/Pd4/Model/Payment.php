<?php
class Df_Pd4_Model_Payment extends Df_Payment_Model_Method_Base {
	/**
	 * @override
	 * @return bool
	 */
	public function canOrder() {return true;}
}