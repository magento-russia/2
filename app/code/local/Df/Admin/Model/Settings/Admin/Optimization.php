<?php
class Df_Admin_Model_Settings_Admin_Optimization extends Df_Core_Model_Settings {
	/** @return boolean */
	public function getFixDoubleStockReindexingOnProductSave() {
		return $this->getYesNo('fix_double_stock_reindexing_on_product_save');
	}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_tweaks_admin/optimization/';}
	/**
	 * @used-by Df_Admin_Model_Settings_Admin::optimization()
	 * @return Df_Admin_Model_Settings_Admin_Optimization
	 */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}