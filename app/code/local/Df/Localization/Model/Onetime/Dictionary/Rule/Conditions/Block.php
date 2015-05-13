<?php
class Df_Localization_Model_Onetime_Dictionary_Rule_Conditions_Block
	extends Df_Localization_Model_Onetime_Dictionary_Rule_Conditions_Abstract {
	/**
	 * @override
	 * @return string
	 */
	protected function getEntityClass() {return Df_Cms_Model_Block::_CLASS;}

	/**
	 * @override
	 * @param Mage_Core_Model_Abstract|Df_Cms_Model_Block $entity
	 * @return array(string => string)
	 */
	protected function getTestMap(Mage_Core_Model_Abstract $entity) {
		return array('id' => $entity->getIdentifier());
	}
}