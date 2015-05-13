<?php
class Df_Localization_Model_Onetime_Type_Poll extends Df_Localization_Model_Onetime_Type {
	/**
	 * @override
	 * @return Df_Poll_Model_Resource_Poll_Collection
	 */
	public function getAllEntities() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Poll_Model_Resource_Poll_Collection::i($loadStoresInfo = true);
		}
		return $this->{__METHOD__};
	}
}