<?php
class Df_Tweaks_Helper_Data extends Mage_Core_Helper_Abstract {
	/** @return Df_Tweaks_Helper_Customer */
	public function customer() {return Df_Tweaks_Helper_Customer::s();}

	/** @return bool */
	public function isItCatalogProductList() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
					rm_handle_presents(Df_Core_Model_Layout_Handle::CATALOG_CATEGORY_VIEW)
				||
					/**
					 * На случай вывода списка товаров через синтаксис {{}}:
					 * {{block
					 * 		type="catalog/product_list"
					 * 		column_count="4"
					 * 		category_id="6"
					 * 		template="catalog/product/list.phtml"
					 * }}
					 */
					rm_handle_presents(Df_Core_Model_Layout_Handle::CMS_PAGE)
				||
					rm_handle_presents(Df_Core_Model_Layout_Handle::CATALOGSEARCH_RESULT_INDEX)
			;
		}
		return $this->{__METHOD__};
	}
	/** @return Df_Tweaks_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}