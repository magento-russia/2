<?php
class Df_Directory_Model_Settings extends Df_Core_Model_Settings {
	/** @return Df_Directory_Model_Settings_Regions */
	public function regionsRu() {return $this->getRegions('regions_ru');}
	/** @return Df_Directory_Model_Settings_Regions */
	public function regionsUa() {return $this->getRegions('regions_ua');}
	/**
	 * @param string $countryPart
	 * @return Df_Directory_Model_Settings_Regions
	 */
	private function getRegions($countryPart) {
		df_param_string_not_empty($countryPart, 0);
		if (!isset($this->{__METHOD__}[$countryPart])) {
			$this->{__METHOD__}[$countryPart] = Df_Directory_Model_Settings_Regions::i($countryPart);
		}
		return $this->{__METHOD__}[$countryPart];
	}

	/** @return Df_Directory_Model_Settings */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}