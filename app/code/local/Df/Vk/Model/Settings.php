<?php
class Df_Vk_Model_Settings extends Df_Core_Model_Settings {
	/** @return Df_Vk_Model_Settings_Widget_Comments */
	public function comments() {return Df_Vk_Model_Settings_Widget_Comments::s();}
	/** @return Df_Vk_Model_Settings_Widget_Groups */
	public function groups() {return Df_Vk_Model_Settings_Widget_Groups::s();}
	/** @return Df_Vk_Model_Settings_Widget_Like */
	public function like() {return Df_Vk_Model_Settings_Widget_Like::s();}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}