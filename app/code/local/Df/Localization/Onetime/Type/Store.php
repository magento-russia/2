<?php
class Df_Localization_Onetime_Type_Store extends Df_Localization_Onetime_Type {
	/**
	 * @override
	 * @return Df_Core_Model_StoreM[]
	 */
	public function getAllEntities() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Mage::app()->getStores($withDefault = true);
		}
		return $this->{__METHOD__};
	}
}