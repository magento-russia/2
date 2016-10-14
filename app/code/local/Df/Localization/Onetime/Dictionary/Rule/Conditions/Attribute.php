<?php
class Df_Localization_Onetime_Dictionary_Rule_Conditions_Attribute
	extends Df_Localization_Onetime_Dictionary_Rule_Conditions_Abstract {
	/**
	 * @override
	 * @return string
	 */
	protected function getEntityClass() {return Df_Catalog_Model_Resource_Eav_Attribute::_C;}

	/**
	 * @override
	 * @param Mage_Core_Model_Abstract|Df_Catalog_Model_Resource_Eav_Attribute $entity
	 * @return array(string => string)
	 */
	protected function getTestMap(Mage_Core_Model_Abstract $entity) {
		return array('code' => $entity->getAttributeCode());
	}
}