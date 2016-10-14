<?php
class Df_Localization_Onetime_Dictionary_Rule_Conditions_Em_Megamenupro
	extends Df_Localization_Onetime_Dictionary_Rule_Conditions_Abstract {
	/**
	 * @override
	 * @return string
	 */
	protected function getEntityClass() {return 'EM_Megamenupro_Model_Megamenupro';}

	/** @noinspection PhpUndefinedClassInspection */
	/**
	 * @override
	 * @param Mage_Core_Model_Abstract|EM_Megamenupro_Model_Megamenupro $entity
	 * @return array(string => string)
	 */
	protected function getTestMap(Mage_Core_Model_Abstract $entity) {
		return array('title' => $entity->getData('name'));
	}
}