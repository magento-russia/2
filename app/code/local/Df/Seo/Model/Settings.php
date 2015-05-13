<?php
class Df_Seo_Model_Settings extends Df_Core_Model_Settings {
	/** @return Df_Seo_Model_Settings_Catalog */
	public function catalog() {return Df_Seo_Model_Settings_Catalog::s();}
	/** @return Df_Seo_Model_Settings_Common */
	public function common() {return Df_Seo_Model_Settings_Common::s();}
	/** @return Df_Seo_Model_Settings_Html */
	public function html() {return Df_Seo_Model_Settings_Html::s();}
	/** @return Df_Seo_Model_Settings_Images */
	public function images() {return Df_Seo_Model_Settings_Images::s();}
	/** @return Df_Seo_Model_Settings_Urls */
	public function urls() {return Df_Seo_Model_Settings_Urls::s();}
	/** @return Df_Seo_Model_Settings */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}