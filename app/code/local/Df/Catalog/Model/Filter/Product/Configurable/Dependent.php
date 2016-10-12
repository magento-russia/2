<?php
/**
 * Возвращает коллекцию товаров-элементов
 * для данного товара системного типа Configurable
 */
class Df_Catalog_Model_Filter_Product_Configurable_Dependent
	extends Df_Core_Model
	implements Zend_Filter_Interface {
	/**
	 * @override
	 * @param mixed $value
	 * @throws Zend_Filter_Exception If filtering $value is impossible
	 * @return Df_Varien_Data_Collection
	 */
	public function filter($value) {
		df_assert($value instanceof Mage_Catalog_Model_Product);
		/** @var Mage_Catalog_Model_Product $value */
		$dependentProducts = Df_Catalog_Model_Product_Type_Configurable::s()->getUsedProducts(null, $value);
		return Df_Varien_Data_Collection::createFromCollection($dependentProducts);
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Catalog_Model_Filter_Product_Configurable_Dependent
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}