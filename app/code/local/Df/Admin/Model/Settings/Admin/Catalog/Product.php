<?php
class Df_Admin_Model_Settings_Admin_Catalog_Product extends Df_Core_Model_Settings {
	/** @return boolean */
	public function getFixBundleJs() {
		return $this->getYesNo('df_tweaks_admin/catalog_product/fix_bundle_js');
	}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}