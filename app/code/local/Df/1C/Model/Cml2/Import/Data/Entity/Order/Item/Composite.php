<?php
/**
 * Это класс моделирует сложную строку заказа:
 * строку, включающую в себя все простые строки заказа для данного товара.
 *
 * Шаблон проектирования Composite:
 *
 * «The composite pattern describes that a group of objects are to be treated
 	 in the same way as a single instance of an object.
 	 The intent of a composite is to "compose" objects into tree structures
 	 to represent part-whole hierarchies.
 	 Implementing the composite pattern
 	 lets clients treat individual objects and compositions uniformly.»
 *
 * @link http://en.wikipedia.org/wiki/Composite_pattern
 */
class Df_1C_Model_Cml2_Import_Data_Entity_Order_Item_Composite
	extends Df_1C_Model_Cml2_Import_Data_Entity_Order_Item {
	/**
	 * @override
	 * @param string $paramName
	 * @param string|int|float $defaultValue[optional]
	 * @return mixed
	 */
	public function getEntityParam($paramName, $defaultValue = null) {
		return $this->getFirstItem()->getEntityParam($paramName, $defaultValue);
	}

	/** @return Df_1C_Model_Cml2_Import_Data_Entity_Order_Item */
	private function getFirstItem() {return df_a($this->getSimpleItems(), 0);}

	/**
	 * Перекрываем этот метод лишь для того,
	 * чтобы не проводить ненужные вычисления свойства $_product
	 * @override
	 * @return Df_Catalog_Model_Product
	 */
	public function getProduct() {return $this->getFirstItem()->getProduct();}

	/** @return array */
	private function getSimpleItems() {return $this->cfg(self::P__SIMPLE_ITEMS);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__SIMPLE_ITEMS, self::V_ARRAY);
	}
	const _CLASS = __CLASS__;
	const P__SIMPLE_ITEMS = 'simple_items';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_1C_Model_Cml2_Import_Data_Entity_Order_Item_Composite
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}