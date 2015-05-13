<?php
class Df_Localization_Model_Onetime_Type_Store extends Df_Localization_Model_Onetime_Type {
	/**
	 * @override
	 * @return Mage_Core_Model_Store[]
	 */
	public function getAllEntities() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Mage::app()->getStores($withDefault = true);
		}
		return $this->{__METHOD__};
	}
}