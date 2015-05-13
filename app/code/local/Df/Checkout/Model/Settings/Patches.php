<?php
class Df_Checkout_Model_Settings_Patches extends Df_Core_Model_Settings {
	/** @return boolean */
	public function fixSalesConvertOrderToQuote() {
		return $this->getYesNo('df_checkout/patches/fix_sales_convert_order_to_quote');
	}
	/** @return Df_Checkout_Model_Settings_Patches */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}