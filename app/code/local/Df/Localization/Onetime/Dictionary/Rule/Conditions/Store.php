<?php
class Df_Localization_Onetime_Dictionary_Rule_Conditions_Store
	extends Df_Localization_Onetime_Dictionary_Rule_Conditions_Abstract {
	/**
	 * @override
	 * @return string
	 */
	protected function getEntityClass() {return Df_Core_Model_StoreM::_C;}

	/**
	 * @override
	 * @param Mage_Core_Model_Abstract|Df_Core_Model_StoreM $entity
	 * @return array(string => string)
	 */
	protected function getTestMap(Mage_Core_Model_Abstract $entity) {
		return array('code' => $entity->getCode());
	}
}