<?php
class Df_Directory_Settings_Countries_Popular extends Df_Core_Model_Settings {
	/**
	 * @used-by Df_Directory_Model_Resource_Country_Collection::toOptionArrayRm()
	 * @return bool
	 */
	public function isEnabled() {return $this->getYesNo('enable');}

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
	 * @used-by Df_Directory_Model_Resource_Country_Collection::toOptionArrayRm()
	 * @return string
	 */
	public function labelAll() {return $this->v('label_all');}

	/**
	 * @used-by Df_Directory_Model_Resource_Country_Collection::toOptionArrayRm()
	 * @return string
	 */
	public function labelPopular() {return $this->v('label_popular');}

	/**
	 * @used-by Df_Directory_Model_Resource_Country_Collection::toOptionArrayRm()
	 * @return bool
	 */
	public function needDuplicate() {return $this->getYesNo('duplicate');}

	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_directory/countries_popular/';}

	/** @return Df_Directory_Settings_Countries_Popular */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}