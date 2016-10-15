<?php
class Df_Directory_Config_Source_Region_Russia extends Df_Core_Model {
	/** @return string[][] */
	public function toOptionArray() {return $this->getAsOptionArray();}

	/** @return string[][] */
	private function getAsOptionArray() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Directory_Model_Resource_Region_Collection $regions */
			$regions = df_h()->directory()->country()->getRussia()->getRegionCollection();
			$regions->setFlag(
				Df_Directory_Model_Handler_ProcessRegionsAfterLoading::FLAG__PREVENT_REORDERING, true
			);
			/** @var string[][] $result */
			$this->{__METHOD__} = $regions->toOptionArray();
		}
		return $this->{__METHOD__};
	}


}