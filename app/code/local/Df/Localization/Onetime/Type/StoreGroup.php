<?php
class Df_Localization_Onetime_Type_StoreGroup extends Df_Localization_Onetime_Type {
	/**
	 * @override
	 * @return Mage_Core_Model_Store_Group[]
	 */
	public function getAllEntities() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Mage::app()->getGroups($withDefault = true);
		}
		return $this->{__METHOD__};
	}
}