<?php
/**
 * Оставляет в коллекции только товары системного типа Configurable
 */
class Df_Catalog_Model_Filter_Product_Collection_Configurable extends Df_Core_Model_Filter_Collection {
	/**
	 * @override
	 * @return Df_Catalog_Model_Validate_Product_Configurable
	 */
	protected function createValidator() {
		return Df_Catalog_Model_Validate_Product_Configurable::i();
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Catalog_Model_Filter_Product_Collection_Configurable
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}