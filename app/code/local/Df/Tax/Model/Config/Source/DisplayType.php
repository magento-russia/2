<?php
class Df_Tax_Model_Config_Source_DisplayType extends Df_Core_Model {
	/** @return array(array(string => string)) */
	public function toOptionArray() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				array(
					array(
						'value' => Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX
						,'label' => df_mage()->taxHelper()->__('Display Excluding Tax')
					)
					,array(
						'value' => Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX
						,'label' => df_mage()->taxHelper()->__('Display Including Tax')
					)
					,array(
						'value' => Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH
						,'label' => df_mage()->taxHelper()->__('Display Including and Excluding Tax')
					)
				)
			;
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
}