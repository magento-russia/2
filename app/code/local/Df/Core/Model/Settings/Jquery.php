<?php
class Df_Core_Model_Settings_Jquery extends Df_Core_Model_Settings {
	/**
	 * @used-by Df_Page_JQueryInjecter::p()
	 * @return bool
	 */
	public function fromGoogle() {return Df_Admin_Config_Source_JqueryLoadMode::google($this->mode());}

	/**
	 * @used-by Df_Page_Helper_Head::needSkipAsJQuery()
	 * @return boolean
	 */
	public function needRemoveExtraneous() {
		return $this->needLoad() && $this->getYesNo('remove_extraneous');
	}

	/**
	 * @used-by needRemoveExtraneous()
	 * @used-by Df_Page_JQueryInjecter::p()
	 * @return bool
	 */
	public function needLoad() {return !Df_Admin_Config_Source_JqueryLoadMode::no($this->mode());}

	/**
	 * @used-by fromGoogle()
	 * @used-by needLoad()
	 * @return string
	 */
	private function mode() {return $this->v('load_mode');}

	/**
	 * @used-by Df_Admin_Model_Settings::jquery()
	 * @return Df_Core_Model_Settings_Jquery
	 */
	public static function s() {
		/** @var Df_Core_Model_Settings_Jquery $r */
		static $r;
		if (!$r) {
			/** @var string $suffix */
			$suffix = df_is_admin() ? '_admin' : '';
			$r = self::sc(__CLASS__, "df_tweaks{$suffix}/other/jquery_");
		}
		return $r;
	}
}