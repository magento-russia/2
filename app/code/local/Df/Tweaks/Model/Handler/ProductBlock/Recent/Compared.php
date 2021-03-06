<?php
class Df_Tweaks_Model_Handler_ProductBlock_Recent_Compared extends Df_Tweaks_Model_Handler_Remover {
	/**
	 * @override
	 * @return string[]
	 */
	protected function getBlockNames() {return array(
		'catalog.compare.sidebar'
		// это имя использует оформительская тема TemplateMela Mega Shop (MAG090172)
		, 'left.catalog.compare.sidebar'
	);}

	/**
	 * @override
	 * @return Df_Tweaks_Model_Settings_Remove
	 */
	protected function getSettings() {return df_cfg()->tweaks()->recentlyComparedProducts();}

	/**
	 * @override
	 * @return bool
	 */
	protected function hasDataToShow() {
		/** @var Mage_Catalog_Helper_Product_Compare $helper */
		$helper = Mage::helper('catalog/product_compare');
		return 0 !== $helper->getItemCount();
	}

	const _CLASS = __CLASS__;
}