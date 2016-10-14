<?php
class Df_Localization_Onetime_Type_LayoutUpdate extends Df_Localization_Onetime_Type {
	/**
	 * @override
	 * @return Df_Core_Model_Layout_Data[]
	 */
	public function getAllEntities() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Core_Model_Layout_Data::c();
			Df_Varien_Data_Collection::unsetDataChanges($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}
}