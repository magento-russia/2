<?php
class Df_Localization_Model_Onetime_Type_Rating extends Df_Localization_Model_Onetime_Type {
	/**
	 * @override
	 * @return Df_Rating_Model_Resource_Rating_Collection
	 */
	public function getAllEntities() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Rating_Model_Rating::c();
		}
		return $this->{__METHOD__};
	}
}