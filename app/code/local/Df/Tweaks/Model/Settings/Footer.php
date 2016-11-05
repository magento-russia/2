<?php
class Df_Tweaks_Model_Settings_Footer extends Df_Core_Model_Settings {
	/** @return boolean */
	public function needUpdateYearInCopyright() {return $this->getYesNo('update_year_in_copyright');}
	/** @return boolean */
	public function removeAdvancedSearchLink() {return $this->getYesNo('remove_advanced_search_link');}
	/** @return boolean */
	public function removeHelpUs() {return $this->getYesNo('remove_help_us');}
	/** @return boolean */
	public function removeSearchTermsLink() {return $this->getYesNo('remove_search_terms_link');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_tweaks/footer/';}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}