<?php
class Df_Seo_Model_Settings_Common extends Df_Core_Model_Settings {
	/** @return boolean */
	public function getEnhancedRussianTransliteration() {
		return $this->getYesNo('df_seo/common/enhanced_russian_transliteration');
	}
	/** @return Df_Seo_Model_Settings_Common */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}