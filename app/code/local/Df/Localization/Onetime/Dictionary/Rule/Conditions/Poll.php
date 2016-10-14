<?php
class Df_Localization_Onetime_Dictionary_Rule_Conditions_Poll
	extends Df_Localization_Onetime_Dictionary_Rule_Conditions_Abstract {
	/**
	 * @override
	 * @return string
	 */
	protected function getEntityClass() {return Df_Poll_Model_Poll::_C;}

	/**
	 * @override
	 * @param Mage_Core_Model_Abstract|Df_Poll_Model_Poll $entity
	 * @return array(string => string)
	 */
	protected function getTestMap(Mage_Core_Model_Abstract $entity) {
		return array('title' => $entity->getPollTitle());
	}
}