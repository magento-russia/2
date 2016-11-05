<?php
class Df_Tweaks_Model_Settings_Labels extends Df_Core_Model_Settings {
	/** @return Df_Admin_Config_Font */
	public function forButtons() {return $this->getFont('buttons');}

	/** @return Df_Admin_Config_Font */
	public function forFormInputs() {return $this->getFont('form_inputs');}

	/** @return Df_Admin_Config_Font */
	public function forSideBlockTitles() {return $this->getFont('side_block_titles');}

	/**
	 * @param string $keyPrefix
	 * @return Df_Admin_Config_Font
	 */
	private function getFont($keyPrefix) {
		df_param_string_not_empty($keyPrefix, 0);
		if (!isset($this->{__METHOD__}[$keyPrefix])) {
			$this->{__METHOD__}[$keyPrefix] = Df_Admin_Config_Font::i(
				'df_tweaks/labels', $keyPrefix
			);
		}
		return $this->{__METHOD__}[$keyPrefix];
	}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}