<?php
class Df_Localization_Onetime_Type_CustomerGroup extends Df_Localization_Onetime_Type {
	/**
	 * @override
	 * @return Df_Customer_Model_Resource_Group_Collection
	 */
	public function getAllEntities() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Customer_Model_Group::c();
		}
		return $this->{__METHOD__};
	}
}