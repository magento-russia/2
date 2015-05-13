<?php
class Df_Seo_Model_Settings_Catalog_Category extends Df_Core_Model_Settings {
	/**
	 * Скрывать ли описание товарного раздела
	 * со всех страниц товарного раздела, кроме первой?
	 * @return boolean
	 */
	public function needHideDescriptionFromNonFirstPages() {
		return $this->getYesNo('df_seo/catalog_category/hide_description_from_non_first_pages');
	}
	/** @return Df_Seo_Model_Settings_Catalog_Category */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}