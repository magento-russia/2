<?php
class Df_AccessControl_Settings extends Df_Core_Model_Settings {
	/** @return boolean */
	public function getAutoExpandAll() {return $this->getYesNo('auto_expand_all');}
	/** @return boolean */
	public function getAutoSelectAncestors() {return $this->getYesNo('auto_select_ancestors');}
	/** @return boolean */
	public function getAutoSelectDescendants() {return $this->getYesNo('auto_select_descendants');}
	/** @return boolean */
	public function getEnabled() {return $this->getYesNo('enabled');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_tweaks_admin/access_control/';}
	/**
	 * @used-by Df_Admin_Model_Settings_Admin::access_control()
	 * @return self
	 */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}