<?php
class Df_Localization_Model_Onetime_Processor_Catalog_Category
	extends Df_Localization_Model_Onetime_Processor_Catalog {
	/** @return string */
	protected function getEntityType() {return Mage_Catalog_Model_Category::ENTITY;}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__ENTITY, Df_Catalog_Model_Category::_CLASS);
	}
}