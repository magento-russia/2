<?php
/** @method Df_Seo_Model_Template_Adapter_Product getAdapter() */
abstract class Df_Seo_Model_Template_Property_Product extends Df_Seo_Model_Template_Property {
	/** @return Df_Catalog_Model_Product */
	public function getProduct() {return $this->getAdapter()->getProduct();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__ADAPTER, Df_Seo_Model_Template_Adapter_Product::_C);
	}
	const _C = __CLASS__;
	const P__ADAPTER = 'adapter';
}