<?php
abstract class Df_Dataflow_Model_Importer_Product_Specialized extends Df_Core_Model {
	/** @return Df_Dataflow_Model_Importer_Product_Specialized */
	abstract public function process();

	/** @return array(string => mixed) */
	protected function getImportedRow() {return $this->cfg(self::P__IMPORTED_ROW);}

	/** @return Df_Catalog_Model_Product */
	protected function getProduct() {return $this->cfg(self::P__PRODUCT);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__IMPORTED_ROW, RM_V_ARRAY)
			->_prop(self::P__PRODUCT, Df_Catalog_Model_Product::_C)
		;
	}
	const _C = __CLASS__;
	const P__IMPORTED_ROW = 'importedRow';
	const P__PRODUCT = 'product';
}