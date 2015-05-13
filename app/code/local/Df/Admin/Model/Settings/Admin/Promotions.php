<?php
class Df_Admin_Model_Settings_Admin_Promotions extends Df_Core_Model_Settings {
	/** @return boolean */
	public function getFixProductsSubselection() {
		return $this->getYesNo('df_tweaks_admin/promotions/fix_products_subselection');
	}
	/** @return Df_Admin_Model_Settings_Admin_Promotions */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}