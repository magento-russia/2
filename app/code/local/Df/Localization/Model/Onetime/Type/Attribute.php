<?php
class Df_Localization_Model_Onetime_Type_Attribute extends Df_Localization_Model_Onetime_Type {
	/**
	 * @override
	 * @return Df_Dataflow_Model_Registry_Collection_Attributes
	 */
	public function getAllEntities() {return df_h()->dataflow()->registry()->attributes();}
}