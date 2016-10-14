<?php
class Df_Catalog_Model_Config_Backend_Catalog_Product_Flat
	extends Mage_Catalog_Model_System_Config_Backend_Catalog_Product_Flat {
	/**
	 * @override
	 * @return Df_Catalog_Model_Config_Backend_Catalog_Product_Flat
	 */
	protected function _afterSave() {
		Mage::dispatchEvent(
			'rm__config_after_save__catalog__frontend__flat_catalog_product', array('object' => $this)
		);
		parent::_afterSave();
		return $this;
	}
}


 