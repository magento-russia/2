<?php
class Df_Directory_Settings_Regions extends Df_Core_Model_Settings {
	/** @return boolean */
	public function getEnabled() {return $this->getYesNo($this->getConfigKeyFull('enabled'));}

	/** @return int[] */
	public function getPriorityRegionIds() {
		if (!isset($this->{__METHOD__})) {
			/** @var int[] $result */
			$result = array();
			for ($i=1; $i <= self::NUM_PRIORITY_REGIONS; $i++) {
				$ids[]= $this->getPriorityRegionIdAtPosition($i);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getConfigKeyCountryPart() {return $this->cfg(self::P__CONFIG_KEY_COUNTRY_PART);}

	/**
	 * @param string $configKeyShort
	 * @return string
	 */
	private function getConfigKeyFull($configKeyShort) {
		df_param_string($configKeyShort, 0);
		return df_concat_xpath('df_directory', $this->getConfigKeyCountryPart(), $configKeyShort);
	}

	/**
	 * @param int $position
	 * @return int
	 */
	private function getPriorityRegionIdAtPosition($position) {
		return rm_int(Mage::getStoreConfig($this->getConfigKeyFull('position_' . $position)));
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__CONFIG_KEY_COUNTRY_PART, RM_V_STRING_NE);
	}
	const NUM_PRIORITY_REGIONS = 5;
	const P__CONFIG_KEY_COUNTRY_PART = 'config_key_country_part';
	/**
	 * @static
	 * @param string $configKeyCountryPart
	 * @return Df_Directory_Settings_Regions
	 */
	public static function i($configKeyCountryPart) {return new self(array(
		self::P__CONFIG_KEY_COUNTRY_PART => $configKeyCountryPart
	));}
}