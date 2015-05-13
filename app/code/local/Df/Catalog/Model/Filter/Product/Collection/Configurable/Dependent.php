<?php
/**
 * Возвращает коллекцию товаров-элементов
 * для данной коллекции товаров системного типа Configurable
 */
class Df_Catalog_Model_Filter_Product_Collection_Configurable_Dependent
	extends Df_Core_Model_Abstract
	implements Zend_Filter_Interface {
	/**
	 * @override
	 * @param array|Traversable $value
	 * @throws Zend_Filter_Exception If filtering $value is impossible
	 * @return Df_Varien_Data_Collection
	 */
	public function filter($value) {
		/** @var Df_Varien_Data_Collection $result */
		$result = new Df_Varien_Data_Collection();
		/** @var Df_Catalog_Model_Filter_Product_Configurable_Dependent $filterDependent */
		$filterDependent = Df_Catalog_Model_Filter_Product_Configurable_Dependent::i();
		foreach ($value as $product) {
			/** @var Mage_Catalog_Model_Product $product */
			$result->addItems($filterDependent->filter($product));
		}
		return $result;
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Catalog_Model_Filter_Product_Collection_Configurable_Dependent
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}