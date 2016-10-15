<?php
class Df_Localization_Onetime_Dictionary_Rule_Conditions_Rating
	extends Df_Localization_Onetime_Dictionary_Rule_Conditions_Abstract {
	/**
	 * @override
	 * @return string
	 */
	protected function getEntityClass() {return Df_Rating_Model_Rating::class;}

	/**
	 * @override
	 * @param Mage_Core_Model_Abstract|Df_Rating_Model_Rating $entity
	 * @return array(string => string)
	 */
	protected function getTestMap(Mage_Core_Model_Abstract $entity) {return array();}
}