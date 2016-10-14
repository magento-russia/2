<?php
class Df_Directory_Settings_Countries_EACU extends Df_Core_Model_Settings {
	/**
	 * @used-by Df_Directory_Model_Resource_Country_Collection::toOptionArrayRm()
	 * @return string[]
	 */
	public function iso2Codes() {
		if (!isset($this->{__METHOD__})) {
			/** @uses Df_Directory_Config_MapItem_Country::getIso2() */
			$this->{__METHOD__} = $this->map('order', 'Df_Directory_Config_MapItem_Country', 'getIso2');
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_directory/countries_eacu/';}

	/** @return Df_Directory_Settings_Countries_EACU */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}