<?php
namespace Df\C1\Cml2\Import\Data\Entity\Order\Item;
use Df\C1\Cml2\Import\Data\Entity\Order;
use Df\C1\Cml2\Import\Data\Entity\Order\Item;
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
 * http://en.wikipedia.org/wiki/Composite_pattern
 */
class Composite extends Item {
	/**
	 * @override
	 * @see \Df\Xml\Parser\Entity::leaf()
	 * @param string $name
	 * @param string|null $default [optional]
	 * @return mixed
	 */
	public function leaf($name, $default = null) {return $this->getFirstItem()->leaf($name, $default);}

	/** @return Item */
	private function getFirstItem() {return dfa($this->getSimpleItems(), 0);}

	/**
	 * Перекрываем этот метод лишь для того,
	 * чтобы не проводить ненужные вычисления свойства $_product
	 * @override
	 * @return \Df_Catalog_Model_Product
	 */
	public function getProduct() {return $this->getFirstItem()->getProduct();}

	/** @return array */
	private function getSimpleItems() {return $this->cfg(self::$P__SIMPLE_ITEMS);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__SIMPLE_ITEMS, DF_V_ARRAY);
	}
	/** @var string */
	private static $P__SIMPLE_ITEMS = 'simple_items';
	/**
	 * @static
	 * @param Order $order
	 * @param Item[] $items
	 * @return self
	 */
	public static function i(Order $order, array $items) {return new self([
		self::P__ENTITY_ORDER => $order, self::$P__SIMPLE_ITEMS => $items, self::$P__E => null
	]);}
}