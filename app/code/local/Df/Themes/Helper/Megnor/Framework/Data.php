<?php
class Df_Themes_Helper_Megnor_Framework_Data extends Megnor_Framework_Helper_Data {
	/**
	 * @override
	 * @param Df_Catalog_Model_Product $_product
	 * @return bool
	 */
	public function isSpecialProduct($_product) {
		return parent::isSpecialProduct(rm_adapt_legacy_object($_product, array('_rule_price')));
	}
}