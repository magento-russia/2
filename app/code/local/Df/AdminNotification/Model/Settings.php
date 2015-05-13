<?php
class Df_AdminNotification_Model_Settings extends Df_Core_Model_Settings {
	/** @return boolean */
	public function getFixReminder() {
		return $this->getYesNo('df_tweaks_admin/system_notifications/fix_reminder');
	}
	/** @return Df_AdminNotification_Model_Settings */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}