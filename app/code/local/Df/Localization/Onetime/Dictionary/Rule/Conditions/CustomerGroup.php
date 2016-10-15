<?php
class Df_Localization_Onetime_Dictionary_Rule_Conditions_CustomerGroup
	extends Df_Localization_Onetime_Dictionary_Rule_Conditions_Abstract {
	/**
	 * @override
	 * @return string
	 */
	protected function getEntityClass() {return Df_Customer_Model_Group::class;}

	/**
	 * @override
	 * @param Mage_Core_Model_Abstract|Df_Customer_Model_Group $entity
	 * @return array(string => string)
	 */
	protected function getTestMap(Mage_Core_Model_Abstract $entity) {
		return array('code' => $entity->getCustomerGroupCode());
	}
}