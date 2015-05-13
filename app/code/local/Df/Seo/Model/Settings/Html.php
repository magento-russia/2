<?php
class Df_Seo_Model_Settings_Html extends Df_Core_Model_Settings {
	/** @return boolean */
	public function getAppendCategoryNameToProductTitleTag() {
		return $this->getYesNo('df_seo/html/append_category_name_to_product_title_tag');
	}
	/** @return string */
	public function getDefaultPatternForProductTitleTag() {
		return $this->getString('df_seo/html/product_title_tag_default_pattern');
	}
	/** @return Df_Seo_Model_Settings_Html */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}