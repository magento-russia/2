<?php
class Df_Admin_Model_Settings_Admin_Editor extends Df_Core_Model_Settings {
	/** @return boolean */
	public function fixHeadersAlreadySent() {return $this->getYesNo('fix_headers_already_sent');}
	/** @return boolean */
	public function fixImages() {return $this->getYesNo('fix_images');}
	/** @return boolean */
	public function useRm() {return $this->getYesNo('use_rm');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_tweaks_admin/editor/';}
	/**
	 * @used-by Df_Admin_Model_Settings_Admin::editor()
	 * @return Df_Admin_Model_Settings_Admin_Editor
	 */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}