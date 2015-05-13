<?php
class Df_Admin_Model_Settings_Admin_System_Tools extends Df_Core_Model_Settings {
	/** @return Df_Admin_Model_Settings_Admin_System_Tools_Compilation */
	public function compilation() {return Df_Admin_Model_Settings_Admin_System_Tools_Compilation::s();}
	/** @return Df_Admin_Model_Settings_Admin_System_Tools */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}