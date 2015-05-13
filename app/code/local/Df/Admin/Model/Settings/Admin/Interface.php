<?php
class Df_Admin_Model_Settings_Admin_Interface extends Df_Core_Model_Settings {
	/** @return Df_Admin_Model_Config_Extractor_Font */
	public function getButtonLabelFont() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Admin_Model_Config_Extractor_Font::i(
					self::CONFIG_GROUP_PATH, self::CONFIG_KEY_PREFIX__BUTTON_LABEL
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Admin_Model_Config_Extractor_Font */
	public function getGridLabelFont() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Admin_Model_Config_Extractor_Font::i(
					self::CONFIG_GROUP_PATH, self::CONFIG_KEY_PREFIX__GRID_LABEL
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Admin_Model_Config_Extractor_Font */
	public function getFormLabelFont() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Admin_Model_Config_Extractor_Font::i(
					self::CONFIG_GROUP_PATH, self::CONFIG_KEY_PREFIX__FORM_LABEL
				)
			;
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	const CONFIG_GROUP_PATH = 'df_tweaks_admin/interface';
	const CONFIG_KEY_PREFIX__FORM_LABEL = 'form_label';
	const CONFIG_KEY_PREFIX__GRID_LABEL = 'grid_label';
	const CONFIG_KEY_PREFIX__BUTTON_LABEL = 'button_label';
	/** @return Df_Admin_Model_Settings_Admin_Interface */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}