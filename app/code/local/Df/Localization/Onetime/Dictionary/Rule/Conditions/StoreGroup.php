<?php
class Df_Localization_Onetime_Dictionary_Rule_Conditions_StoreGroup
	extends Df_Localization_Onetime_Dictionary_Rule_Conditions_Abstract {
	/**
	 * @override
	 * @return string
	 */
	protected function getEntityClass() {return 'Mage_Core_Model_Store_Group';}

	/**
	 * @override
	 * @param Mage_Core_Model_Abstract|Mage_Core_Model_Store_Group $entity
	 * @return array(string => string)
	 */
	protected function getTestMap(Mage_Core_Model_Abstract $entity) {
		return array('title' => $entity->getName());
	}
}