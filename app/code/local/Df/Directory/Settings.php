<?php
class Df_Directory_Settings extends Df_Core_Model_Settings {
	/**
	 * @param string $iso2
	 * @return Df_Directory_Settings_Regions
	 */
	public function getRegions($iso2) {
		df_param_iso2($iso2, 0);
		if (!isset($this->{__METHOD__}[$iso2])) {
			$this->{__METHOD__}[$iso2] =
				Df_Directory_Settings_Regions::i('regions_' . mb_strtolower($iso2))
			;
		}
		return $this->{__METHOD__}[$iso2];
	}

	/** @return Df_Directory_Settings_Regions */
	public function regionsKz() {return $this->getRegions('KZ');}
	/** @return Df_Directory_Settings_Regions */
	public function regionsRu() {return $this->getRegions('RU');}
	/** @return Df_Directory_Settings_Regions */
	public function regionsUa() {return $this->getRegions('UA');}

	/** @return Df_Directory_Settings */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}