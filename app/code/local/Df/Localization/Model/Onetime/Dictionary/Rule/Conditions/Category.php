<?php
class Df_Localization_Model_Onetime_Dictionary_Rule_Conditions_Category
	extends Df_Localization_Model_Onetime_Dictionary_Rule_Conditions_Abstract {
	/**
	 * @override
	 * @return string
	 */
	protected function getEntityClass() {return Df_Catalog_Model_Category::_CLASS;}

	/**
	 * @override
	 * @param Mage_Core_Model_Abstract|Df_Catalog_Model_Category $entity
	 * @return array(string => string)
	 */
	protected function getTestMap(Mage_Core_Model_Abstract $entity) {
		return array(
			'name' => $entity->getName()
			,'parent' =>
				!$entity->getParentCategory() ? '' : $entity->getParentCategory()->getName()
			,'parent_url_key' =>
				!$entity->getParentCategory() ? '' : $entity->getParentCategory()->getUrlKey()
			,'url_key' => $entity->getUrlKey()
		);
	}
}