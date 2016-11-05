<?php
class Df_Seo_Model_Settings_Urls extends Df_Core_Model_Settings {
	/**
	 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
	 * @return boolean
	 */
	public function getAddCategoryToProductUrl($store = null) {
		return $this->getYesNo(Mage_Catalog_Helper_Product::XML_PATH_PRODUCT_URL_USE_CATEGORY, $store);
	}
	/** @return boolean */
	public function getFixAddCategoryToProductUrl() {
		return $this->getYesNo('df_seo/urls/fix_add_category_to_product_url');
	}
	/** @return boolean */
	public function getPreserveCyrillic() {
		return $this->getYesNo('df_seo/urls/preserve_cyrillic');
	}
	/** @return boolean */
	public function needRedirectToCanonicalProductUrl() {
		return $this->getYesNo('df_seo/urls/redirect_to_canonical_product_url');
	}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}