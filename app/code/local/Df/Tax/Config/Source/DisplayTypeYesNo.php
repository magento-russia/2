<?php
class Df_Tax_Config_Source_DisplayTypeYesNo {
	/** @return array(array(string => string)) */
	public function toOptionArray() {
		return df_map_to_options(array('Display Excluding Tax', 'Display Including Tax'), 'Mage_Tax');
	}
}