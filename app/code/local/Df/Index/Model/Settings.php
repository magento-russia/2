<?php
class Df_Index_Model_Settings extends Df_Core_Model_Settings {
	/** @return int */
	public function getVarcharLength() {
		return $this->nat0('df_tweaks_admin/system_indices/varchar_length');
	}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}