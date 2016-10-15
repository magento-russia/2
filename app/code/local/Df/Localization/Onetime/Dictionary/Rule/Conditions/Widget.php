<?php
class Df_Localization_Onetime_Dictionary_Rule_Conditions_Widget
	extends Df_Localization_Onetime_Dictionary_Rule_Conditions_Abstract {
	/**
	 * @override
	 * @return string
	 */
	protected function getEntityClass() {return Df_Widget_Model_Widget_Instance::class;}

	/**
	 * @override
	 * @param Mage_Core_Model_Abstract|Df_Widget_Model_Widget_Instance $entity
	 * @return array(string => string)
	 */
	protected function getTestMap(Mage_Core_Model_Abstract $entity) {
		return array(
			'title' => $entity->getTitle()
			,'type' => $entity->getInstanceType()
			,'package' => $entity->getPackage()
			,'theme' => $entity->getTheme()
		);
	}
}