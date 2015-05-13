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

	const _CLASS = __CLASS__;
	/** @return Df_Catalog_Model_Admin_Notifier_Flat_Product */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}