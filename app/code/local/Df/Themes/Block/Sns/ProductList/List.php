<?php
/** @noinspection PhpUndefinedClassInspection */
class Df_Themes_Block_Sns_ProductList_List extends Sns_ProductList_Block_List {
	/**
	 * @override
	 * @return Mage_Eav_Model_Entity_Collection_Abstract
	 */
	protected function _getProductCollection() {
		if (is_null($this->{'_productCollection'})) {
			df_adapt_legacy_object($this);
		}
		/** @noinspection PhpUndefinedClassInspection */
		return parent::_getProductCollection();
	}
}