<?php
class Df_Directory_Model_Settings_Regions extends Df_Core_Model_Settings {
	/** @return boolean */
	public function getEnabled() {
		return $this->getYesNo($this->getConfigKeyFull('enabled'));
	}

	/**
	 * @param int $position
	 * @return int
	 */
	public function getPriorityRegionIdAtPosition($position) {
		df_param_integer($position, 0);
		df_param_between($position, 0, 1, self::NUM_PRIORITY_REGIONS);
		return rm_int(Mage::getStoreConfig($this->getConfigKeyFull(rm_sprintf('position_%d', $position))));
	}

	/** @return string */
	private function getConfigKeyCountryPart() {return $this->cfg(self::P__CONFIG_KEY_COUNTRY_PART);}

	/**
	 * @param string $configKeyShort
	 * @return string
	 */
	private function getConfigKeyFull($configKeyShort) {
		df_param_string($configKeyShort, 0);
		return rm_config_key('df_directory', $this->getConfigKeyCountryPart(), $configKeyShort);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__CONFIG_KEY_COUNTRY_PART, self::V_STRING_NE);
	}
	const NUM_PRIORITY_REGIONS = 5;
	const P__CONFIG_KEY_COUNTRY_PART = 'config_key_country_part';
	/**
	 * @static
	 * @param string $configKeyCountryPart
	 * @return Df_Directory_Model_Settings_Regions
	 */
	public static function i($configKeyCountryPart) {return new self(array(
		self::P__CONFIG_KEY_COUNTRY_PART => $configKeyCountryPart
	));}
}