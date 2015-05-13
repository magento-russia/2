<?php
class Df_Parser_Model_Setup_2_22_8 extends Df_Core_Model_Setup {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		if (Df_Catalog_Model_Resource_Installer_Attribute::s()->getAttributeId('catalog_category', 'lamoda__path')) {
			Df_Catalog_Model_Resource_Installer_Attribute::s()->removeAttribute('catalog_category', 'lamoda__path');
		}
		Df_Catalog_Model_Resource_Installer_Attribute::s()->addAdministrativeCategoryAttribute(
			$attributeId = Df_Catalog_Model_Category::P__EXTERNAL_URL
			,$attributeLabel = 'Веб-адрес на сайте-доноре'
		);
		/**
		 * Вот в таких ситуациях, когда у нас меняется структура прикладного типа товаров,
		 * нам нужно сбросить глобальный кэш EAV.
		 */
		rm_eav_reset($reindexFlat = false);
		Df_Catalog_Model_Category::reindexFlat();
	}

	/** @return Df_Parser_Model_Setup_2_22_8 */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}