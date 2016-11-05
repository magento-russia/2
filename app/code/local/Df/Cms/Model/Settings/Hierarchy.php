<?php
class Df_Cms_Model_Settings_Hierarchy extends Df_Core_Model_Settings {
	/** @return boolean */
	public function isEnabled() {return $this->getYesNo('enabled');}
	/** @return boolean */
	public function needAddToCatalogMenu() {return $this->getYesNo('add_to_catalog_menu');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_cms/hierarchy/';}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}