<?php
class Df_Catalog_Model_Admin_Notifier_Flat_Product extends Df_Catalog_Model_Admin_Notifier_Flat {
	/**
	 * @override
	 * @return string
	 */
	protected function getConfigPathSuffix() {return 'flat_catalog_product';}
	/**
	 * @override
	 * @return string
	 */
	protected function getTableTypeInGenitiveCase() {return 'товаров';}

	/**
	 * @used-by Df_Catalog_Observer::df__config_after_save__catalog__frontend__flat_catalog_product()
	 * @return Df_Catalog_Model_Admin_Notifier_Flat_Product
	 */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}