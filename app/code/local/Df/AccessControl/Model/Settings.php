<?php
class Df_AccessControl_Model_Settings extends Df_Core_Model_Settings {
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
	/** @return Df_AccessControl_Model_Settings */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}