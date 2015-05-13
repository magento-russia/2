<?php
class Df_Localization_Model_Onetime_Type_Widget extends Df_Localization_Model_Onetime_Type {
	/**
	 * @override
	 * @return Df_Widget_Model_Resource_Widget_Instance_Collection
	 */
	public function getAllEntities() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Widget_Model_Resource_Widget_Instance_Collection::i($forUpdating = true)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getProcessorClassSuffix() {return 'Cms_Widget';}
}