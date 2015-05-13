<?php
class Df_Localization_Model_Onetime_Type_Block extends Df_Localization_Model_Onetime_Type {
	/**
	 * @override
	 * @return Df_Cms_Model_Resource_Block_Collection
	 */
	public function getAllEntities() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Cms_Model_Resource_Block_Collection::i($loadStoresInfo = true)->withoutOrphans()
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getProcessorClassSuffix() {return 'Cms_Block';}
}