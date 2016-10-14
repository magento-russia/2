<?php
class Df_Localization_Onetime_Dictionary_Rule_Conditions_Product_Option
	extends Df_Localization_Onetime_Dictionary_Rule_Conditions_Abstract {
	/**
	 * @override
	 * @return string
	 */
	protected function getEntityClass() {return Df_Catalog_Model_Product_Option_Title::_C;}

	/**
	 * @override
	 * @param Mage_Core_Model_Abstract|Df_Catalog_Model_Product_Option_Title $entity
	 * @return array(string => string)
	 */
	protected function getTestMap(Mage_Core_Model_Abstract $entity) {return array();}
}