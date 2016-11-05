<?php
class Df_Admin_Model_Settings_Admin_System_Tools_Compilation extends Df_Core_Model_Settings {
	/** @return boolean */
	public function getFix() {
		return $this->getYesNo('df_tweaks_admin/system_tools_compilation/fix');
	}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}