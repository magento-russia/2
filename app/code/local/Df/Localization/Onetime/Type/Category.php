<?php
class Df_Localization_Onetime_Type_Category extends Df_Localization_Onetime_Type {
	/**
	 * @override
	 * @return Df_Dataflow_Model_Registry_MultiCollection_Categories
	 */
	public function getAllEntities() {
		return Df_Dataflow_Model_Registry_MultiCollection_Categories::s();
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getProcessorClassSuffix() {return 'Catalog_Category';}
}