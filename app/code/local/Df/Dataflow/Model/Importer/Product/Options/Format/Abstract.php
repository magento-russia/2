<?php
abstract class Df_Dataflow_Model_Importer_Product_Options_Format_Abstract
	extends Df_Core_Model {
	/**
	 * @abstract
	 * @return Df_Dataflow_Model_Importer_Product_Options_Format_Abstract
	 */
	public abstract function process();

	/**
	 * @abstract
	 * @return string
	 */
	protected abstract function getPattern();

	/**
	 * @param string $key
	 * @return bool
	 */
	public function canProcess($key) {return 1 === preg_match($this->getPattern(), $key);}

	/** @return Df_Catalog_Model_Product */
	protected function getProduct() {
		return $this->_getData(self::P__PRODUCT);
	}

	/** @return string */
	protected function getImportedKey() {return $this->cfg(self::P__IMPORTED_KEY);}

	/** @return string|null */
	protected function getImportedValue() {return $this->cfg(self::P__IMPORTED_VALUE);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__IMPORTED_KEY, DF_V_STRING_NE)
			->_prop(self::P__IMPORTED_VALUE, DF_V_STRING)
			->_prop(self::P__PRODUCT, self::P__PRODUCT_TYPE)
		;
	}

	const P__IMPORTED_KEY = 'importedKey';
	const P__IMPORTED_VALUE = 'importedValue';
	const P__PRODUCT = 'product';
	const P__PRODUCT_TYPE = 'Mage_Catalog_Model_Product';
}