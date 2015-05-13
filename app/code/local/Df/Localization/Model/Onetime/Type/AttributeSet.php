<?php
class Df_Localization_Model_Onetime_Type_AttributeSet
	extends Df_Localization_Model_Onetime_Type {
	/**
	 * @override
	 * @return Df_Dataflow_Model_Registry_Collection_AttributeSets
	 */
	public function getAllEntities() {return df_h()->dataflow()->registry()->attributeSets();}
}