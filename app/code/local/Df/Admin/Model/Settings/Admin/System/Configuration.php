<?php
class Df_Admin_Model_Settings_Admin_System_Configuration extends Df_Core_Model_Settings {
	/** @return boolean */
	public function getRemindFieldsetToggleState() {
		return $this->getYesNo('df_tweaks_admin/system_config/remind_fieldset_toggle_state');
	}
	/** @return Df_Admin_Model_Settings_Admin_System_Configuration */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}