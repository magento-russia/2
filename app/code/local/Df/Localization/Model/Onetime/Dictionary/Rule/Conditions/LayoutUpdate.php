<?php
class Df_Localization_Model_Onetime_Dictionary_Rule_Conditions_LayoutUpdate
	extends Df_Localization_Model_Onetime_Dictionary_Rule_Conditions_Abstract {
	/**
	 * @override
	 * @return string
	 */
	protected function getEntityClass() {return 'Df_Core_Model_Layout_Data';}

	/**
	 * @override
	 * @param Mage_Core_Model_Abstract|Df_Core_Model_Layout_Data $entity
	 * @return array(string => string)
	 */
	protected function getTestMap(Mage_Core_Model_Abstract $entity) {return array();}
}