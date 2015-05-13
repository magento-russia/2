<?php
class Df_Admin_Model_Settings_Admin_Jquery extends Df_Core_Model_Settings_Jquery {
	/**
	 * @override
	 * @return string
	 */
	public function getLoadMode() {return $this->getString('jquery_load_mode');}
	/**
	 * @override
	 * @return boolean
	 */
	public function needRemoveExtraneous() {return $this->getYesNo('jquery_remove_extraneous');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_tweaks_admin/other/';}
	const _CLASS = __CLASS__;
	/** @return Df_Admin_Model_Settings_Admin_Jquery */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}