<?php
class Df_Admin_Model_Settings_Admin_System extends Df_Core_Model_Settings {
	/** @return Df_Admin_Model_Settings_Admin_System_Configuration */
	public function configuration() {return Df_Admin_Model_Settings_Admin_System_Configuration::s();}
	/** @return Df_AdminNotification_Model_Settings */
	public function notifications() {return Df_AdminNotification_Model_Settings::s();}
	/** @return Df_Admin_Model_Settings_Admin_System_Tools */
	public function tools() {return Df_Admin_Model_Settings_Admin_System_Tools::s();}
	/** @return Df_Admin_Model_Settings_Admin_System */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}