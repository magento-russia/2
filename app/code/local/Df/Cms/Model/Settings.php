<?php
class Df_Cms_Model_Settings extends Df_Core_Model_Settings {
	/** @return Df_Cms_Model_Settings_Hierarchy */
	public function hierarchy() {return Df_Cms_Model_Settings_Hierarchy::s();}
	/** @return Df_Cms_Model_Settings_Versioning */
	public function versioning() {return Df_Cms_Model_Settings_Versioning::s();}
	const _C = __CLASS__;
	/** @return Df_Cms_Model_Settings */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}