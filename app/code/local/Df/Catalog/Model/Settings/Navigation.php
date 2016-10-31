<?php
class Df_Catalog_Model_Settings_Navigation extends Df_Core_Model_Settings {
	/** @return boolean */
	public function getEnabled() {return $this->getYesNo('enabled');}
	/** @return int */
	public function getNumberOfColumns() {return $this->nat('number_of_columns');}
	/** @return string */
	public function getPosition() {return $this->v('position');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_tweaks/illustrated_catalog_navigation/';}
	/** @return Df_Catalog_Model_Settings_Navigation */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}