<?php
abstract class Df_Localization_Model_Onetime_Processor_Catalog
	extends Df_Localization_Model_Onetime_Processor_Entity {
	/** @return string */
	abstract protected function getEntityType();

	/**
	 * @override
	 * @return string
	 */
	protected function getTitlePropertyName() {return 'name';}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getTranslatableProperties() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_mage()->eav()->configSingleton()->getEntityAttributeCodes(
					$this->getEntityType()
				)
			;
		}
		return $this->{__METHOD__};
	}
}


 