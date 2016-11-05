<?php
class Df_Cms_Model_Settings_Versioning extends Df_Core_Model_Settings {
	/** @return boolean */
	public function getDefault() {return $this->getYesNo('default');}
	/** @return boolean */
	public function isEnabled() {return $this->getYesNo('enabled');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_cms/versioning/';}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}