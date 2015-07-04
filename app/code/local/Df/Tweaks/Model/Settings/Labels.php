<?php
class Df_Tweaks_Model_Settings_Labels extends Df_Core_Model_Settings {
	/** @return Df_Admin_Model_Config_Extractor_Font */
	public function getFontForButton() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Admin_Model_Config_Extractor_Font::i(
				self::CONFIG_GROUP_PATH, self::CONFIG_KEY_PREFIX__BUTTON
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Admin_Model_Config_Extractor_Font */
	public function getFontForFormInputs() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Admin_Model_Config_Extractor_Font::i(
				self::CONFIG_GROUP_PATH, 'form_inputs'
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Admin_Model_Config_Extractor_Font */
	public function getFontForSideBlockLabel() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Admin_Model_Config_Extractor_Font::i(
				self::CONFIG_GROUP_PATH, self::CONFIG_KEY_PREFIX__SIDE_BLOCK
			);
		}
		return $this->{__METHOD__};
	}
	const CONFIG_GROUP_PATH = 'df_tweaks/labels';
	const CONFIG_KEY_PREFIX__BUTTON = 'button';
	const CONFIG_KEY_PREFIX__SIDE_BLOCK = 'side_block';
	/** @return Df_Tweaks_Model_Settings_Labels */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}