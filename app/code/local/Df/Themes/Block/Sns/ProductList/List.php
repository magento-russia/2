<?php
class Df_Themes_Block_Sns_ProductList_List extends Sns_ProductList_Block_List {
	/**
	 * @override
	 * @return Mage_Eav_Model_Entity_Collection_Abstract
	 */
	protected function _getProductCollection() {
		if (is_null($this->_productCollection)) {
			rm_adapt_legacy_object($this);
		}
		return parent::_getProductCollection();
	}
}