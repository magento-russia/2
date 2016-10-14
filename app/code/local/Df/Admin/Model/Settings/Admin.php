<?php
class Df_Admin_Model_Settings_Admin extends Df_Core_Model_Settings {
	/** @return Df_Admin_Model_Settings_Admin_Interface */
	public function _interface() {return Df_Admin_Model_Settings_Admin_Interface::s();}
	/** @return Df_AccessControl_Model_Settings */
	public function access_control() {return Df_AccessControl_Model_Settings::s();}
	/** @return Df_Admin_Model_Settings_Admin_Catalog */
	public function catalog() {return Df_Admin_Model_Settings_Admin_Catalog::s();}
	/** @return Df_Admin_Model_Settings_Admin_Editor */
	public function editor() {return Df_Admin_Model_Settings_Admin_Editor::s();}
	/** @return Df_Logging_Model_Settings */
	public function logging() {return Df_Logging_Model_Settings::s();}
	/** @return Df_Admin_Model_Settings_Admin_Optimization */
	public function optimization() {return Df_Admin_Model_Settings_Admin_Optimization::s();}
	/** @return Df_Admin_Model_Settings_Admin_Promotions */
	public function promotions() {return Df_Admin_Model_Settings_Admin_Promotions::s();}
	/** @return Df_Admin_Model_Settings_Admin_Sales */
	public function sales() {return Df_Admin_Model_Settings_Admin_Sales::s();}
	/** @return Df_Admin_Model_Settings_Admin_System */
	public function system() {return Df_Admin_Model_Settings_Admin_System::s();}
	/** @return Df_Admin_Model_Settings_Admin */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}