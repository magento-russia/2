<?php
class Df_Admin_Model_Settings_Admin_Sales_Customers extends Df_Core_Model_Settings {
	/** @return boolean */
	public function getEnableWebsiteChanging() {
		return $this->getYesNo('df_tweaks_admin/sales_customers/enable_website_changing');
	}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}