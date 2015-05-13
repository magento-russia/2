<?php
class Df_Localization_Model_Onetime_Type_Product extends Df_Localization_Model_Onetime_Type {
	/**
	 * @override
	 * @return Df_Dataflow_Model_Registry_MultiCollection_Products
	 */
	public function getAllEntities() {
		return Df_Dataflow_Model_Registry_MultiCollection_Products::s();
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getProcessorClassSuffix() {return 'Catalog_Product';}
}