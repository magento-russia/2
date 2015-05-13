<?php
class Df_Localization_Model_Onetime_Type_Product_Option extends Df_Localization_Model_Onetime_Type {
	/**
	 * @override
	 * @return Df_Catalog_Model_Resource_Product_Option_Title_Collection
	 */
	public function getAllEntities() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Catalog_Model_Product_Option_Title::c();
			Df_Varien_Data_Collection::unsetDataChanges($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}
}