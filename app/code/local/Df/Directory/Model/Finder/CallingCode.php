<?php
class Df_Directory_Model_Finder_CallingCode extends Df_Core_Model {
	/**
	 * @param Mage_Directory_Model_Country $country
	 * @return string
	 */
	public function getAlternativeByCountry(Mage_Directory_Model_Country $country) {
		return $this->getValue($country, 'calling-code-alternative');
	}

	/**
	 * @param Mage_Directory_Model_Country $country
	 * @return string
	 */
	public function getByCountry(Mage_Directory_Model_Country $country) {
		return $this->getValue($country, 'calling-code');
	}

	/**
	 * @param Mage_Directory_Model_Country $country
	 * @param string $key
	 * @return string
	 */
	private function getValue(Mage_Directory_Model_Country $country, $key) {
		/** @var string $iso3 */
		$iso3 = $country->getIso3Code();
		if (!isset($this->{__METHOD__}[$iso3][$key])) {
			$this->{__METHOD__}[$iso3][$key] = rm_leaf_sne(rm_config_node('df/countries', $iso3, $key));
		}
		return $this->{__METHOD__}[$iso3][$key];
	}

	/** @return Df_Directory_Model_Finder_CallingCode */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}