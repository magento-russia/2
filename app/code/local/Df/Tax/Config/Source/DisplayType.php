<?php
class Df_Tax_Config_Source_DisplayType {
	/** @return array(array(string => string)) */
	public function toOptionArray() {
		return df_map_to_options(array(
			Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX => 'Display Excluding Tax'
			, Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX => 'Display Including Tax'
			, Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH => 'Display Including and Excluding Tax'
		), 'Mage_Tax');
	}
}