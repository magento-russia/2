<?php
class Df_Shipping_Exception_MethodNotApplicable extends Df_Shipping_Exception {
	/**
	 * @override
	 * @return bool
	 */
	public function needNotifyAdmin() {return false;}

	/**
	 * @override
	 * @return bool
	 */
	public function needNotifyDeveloper() {return false;}
}