<?php
class Df_Admin_Model_Settings_Admin_Interface extends Df_Core_Model_Settings {
	/** @return Df_Admin_Config_Font */
	public function getButtonLabelFont() {return $this->getFont('button_label');}

	/** @return Df_Admin_Config_Font */
	public function getGridLabelFont() {return $this->getFont('grid_label');}

	/** @return Df_Admin_Config_Font */
	public function getFormLabelFont() {return $this->getFont('form_label');}

	/**
	 * @param string $field
	 * @return Df_Admin_Config_Font
	 */
	private function getFont($field) {
		if (!isset($this->{__METHOD__}[$field])) {
			$this->{__METHOD__}[$field] = Df_Admin_Config_Font::i(
				'df_tweaks_admin/interface', $field
			);
		}
		return $this->{__METHOD__}[$field];
	}

	/**
	 * @used-by Df_Admin_Model_Settings_Admin::_interface()
	 * @return Df_Admin_Model_Settings_Admin_Interface
	 */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}