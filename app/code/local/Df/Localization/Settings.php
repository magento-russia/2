<?php
class Df_Localization_Settings extends Df_Core_Model_Settings {
	/** @return Df_Localization_Settings_Area */
	public function current() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getArea(df_is_admin() ? 'admin' : 'frontend');
		}
		return $this->{__METHOD__};
	}
	/** @return Df_Localization_Settings_Area */
	public function email() {return $this->getArea('email');}
	/**
	 * @param string $name
	 * @return Df_Localization_Settings_Area
	 */
	private function getArea($name) {
		df_param_string($name, 0);
		if (!isset($this->{__METHOD__}[$name])) {
			$this->{__METHOD__}[$name] = Df_Localization_Settings_Area::i($name);
		}
		return $this->{__METHOD__}[$name];
	}
	/** @return Df_Localization_Settings */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}