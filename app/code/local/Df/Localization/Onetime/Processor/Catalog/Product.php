<?php
class Df_Localization_Onetime_Processor_Catalog_Product
	extends Df_Localization_Onetime_Processor_Catalog {
	/** @return string */
	protected function getEntityType() {return Mage_Catalog_Model_Product::ENTITY;}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__ENTITY, Df_Catalog_Model_Product::_C);
	}
}