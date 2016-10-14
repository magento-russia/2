<?php
class Df_Localization_Onetime_Dictionary_Rule_Conditions_AttributeSet
	extends Df_Localization_Onetime_Dictionary_Rule_Conditions_Abstract {
	/**
	 * @override
	 * @return string
	 */
	protected function getEntityClass() {return Df_Eav_Model_Entity_Attribute_Set::_C;}

	/**
	 * @override
	 * @param Mage_Core_Model_Abstract|Df_Eav_Model_Entity_Attribute_Set $entity
	 * @return array(string => string)
	 */
	protected function getTestMap(Mage_Core_Model_Abstract $entity) {
		return array('name' => $entity->getAttributeSetName());
	}
}