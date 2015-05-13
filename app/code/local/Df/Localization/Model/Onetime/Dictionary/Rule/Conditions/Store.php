<?php
class Df_Localization_Model_Onetime_Dictionary_Rule_Conditions_Store
	extends Df_Localization_Model_Onetime_Dictionary_Rule_Conditions_Abstract {
	/**
	 * @override
	 * @return string
	 */
	protected function getEntityClass() {return 'Mage_Core_Model_Store';}

	/**
	 * @override
	 * @param Mage_Core_Model_Abstract|Mage_Core_Model_Store $entity
	 * @return array(string => string)
	 */
	protected function getTestMap(Mage_Core_Model_Abstract $entity) {
		return array('code' => $entity->getCode());
	}
}