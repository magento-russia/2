<?php
class Df_Tax_Model_Config_Source_DisplayTypeYesNo extends Df_Core_Model_Abstract {
	/** @return array(array(string => string)) */
	public function toOptionArray() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				array(
					array(
						'value' => 0
						,'label' => df_mage()->taxHelper()->__('Display Excluding Tax')
					)
					,array(
						'value' => 1
						,'label' => df_mage()->taxHelper()->__('Display Including Tax')
					)
				)
			;
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
}