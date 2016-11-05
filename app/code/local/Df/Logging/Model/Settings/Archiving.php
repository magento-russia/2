<?php
class Df_Logging_Model_Settings_Archiving extends Df_Core_Model_Settings {
	/** @return int */
	public function getLifetime() {return $this->nat('lifetime');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_tweaks_admin/logging__archiving/';}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}