<?php
class Df_Localization_Onetime_Dictionary_Rule_Conditions_Page
	extends Df_Localization_Onetime_Dictionary_Rule_Conditions_Abstract {
	/**
	 * @override
	 * @return string
	 */
	protected function getEntityClass() {return Df_Cms_Model_Page::class;}

	/**
	 * @override
	 * @param Mage_Core_Model_Abstract|Df_Cms_Model_Page $entity
	 * @return array(string => string)
	 */
	protected function getTestMap(Mage_Core_Model_Abstract $entity) {
		return array(
			'title' => $entity->getTitle()
			,'url_key' => $entity->getIdentifier()
		);
	}
}