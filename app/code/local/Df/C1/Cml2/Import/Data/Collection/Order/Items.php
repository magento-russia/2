<?php
namespace Df\C1\Cml2\Import\Data\Collection\Order;
class Items extends \Df\C1\Cml2\Import\Data\Collection {
	/**
	 * @param int $productId
	 * @return \Df\C1\Cml2\Import\Data\Entity\Order\Item
	 */
	public function getItemByProductId($productId) {
		df_param_integer($productId, 0);
		df_param_between($productId, 0, 1);
		/** @var \Df\C1\Cml2\Import\Data\Entity\Order\Item $result */
		$result = dfa($this->getMapFromProductIdToOrderItem(), $productId);
		df_assert($result instanceof \Df\C1\Cml2\Import\Data\Entity\Order\Item);
		return $result;
	}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::initItems()
	 * @used-by \Df\Xml\Parser\Collection::getItems()
	 * @return void
	 */
	protected function initItems() {
		/** @var \Df\C1\Cml2\Import\Data\Entity\Order\Item[] $result */
		$result = array();
		/** @var \Df\C1\Cml2\Import\Data\Entity\Order\Item[] $mapFromProductsToOrderItems */
		$mapFromProductsToOrderItems = array();
		foreach ($this->getImportEntitiesAsSimpleXMLElementArray() as $e) {
			/** @var \Df\Xml\X $e */
			/** @var \Df\C1\Cml2\Import\Data\Entity\Order\Item $orderItem */
			$orderItem = $this->createItem($e);
			df_assert($orderItem instanceof \Df\C1\Cml2\Import\Data\Entity\Order\Item);
			/** @var int $productId */
			$productId = $orderItem->getProduct()->getId();
			df_assert_integer($productId);
			/** @var \Df\C1\Cml2\Import\Data\Entity\Order\Item[] $orderItemsForTheProduct */
			$orderItemsForTheProduct = df_nta(dfa($mapFromProductsToOrderItems, $productId));
			$orderItemsForTheProduct[]= $orderItem;
			$mapFromProductsToOrderItems[$productId] = $orderItemsForTheProduct;
		}
		foreach ($mapFromProductsToOrderItems as $orderItemsForTheProduct) {
			/** @var \Df\C1\Cml2\Import\Data\Entity\Order\Item[] $orderItemsForTheProduct */
			/** @var int $orderItemsCount */
			$orderItemsCount = count($orderItemsForTheProduct);
			df_assert_gt0($orderItemsCount);
			$result[]=
				1 === $orderItemsCount
				? dfa($orderItemsForTheProduct, 0)
				: \Df\C1\Cml2\Import\Data\Entity\Order\Item\Composite::i(
					$this->getEntityOrder(), $orderItemsForTheProduct
				)
			;
		}
		/**
		 * 2015-08-04
		 * Вообще, неправильно, что мы инициализируем свойство @uses _items таким грубым способом.
		 * Надо будет переработать всю логику обработки заказов.
		 */
		$this->_items = $result;
	}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return \Df\C1\Cml2\Import\Data\Entity\Order\Item::class;}

	/**
	 * @override
	 * @return array(string = mixed)
	 */
	protected function itemParams() {
		return array(\Df\C1\Cml2\Import\Data\Entity\Order\Item::P__ENTITY_ORDER => $this->getEntityOrder());
	}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemPath()
	 * @return string|string[]
	 */
	protected function itemPath() {return 'Товары/Товар';}

	/** @return \Df\C1\Cml2\Import\Data\Entity\Order */
	private function getEntityOrder() {
		return $this->cfg(self::$P__ENTITY_ORDER);
	}

	/** @return array(int => \Df\C1\Cml2\Import\Data\Entity\Order\Item) */
	private function getMapFromProductIdToOrderItem() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(int => \Df\C1\Cml2\Import\Data\Entity\Order\Item) $result */
			$result = array();
			foreach ($this->getItems() as $entityOrderItem) {
				/** @var \Df\C1\Cml2\Import\Data\Entity\Order\Item $entityOrderItem */
				/** @var int $productId */
				$productId = $entityOrderItem->getProduct()->getId();
				df_assert_gt0($productId);
				df_assert(is_null(dfa($result, $productId)));
				$result[$productId] = $entityOrderItem;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__ENTITY_ORDER, \Df\C1\Cml2\Import\Data\Entity\Order::class);
	}
	/** @var string */
	private static $P__ENTITY_ORDER = 'entity_order';
	/**
	 * @used-by \Df\C1\Cml2\Import\Data\Entity\Order::getItems()
	 * @static
	 * @param \Df\Xml\X $e
	 * @param \Df\C1\Cml2\Import\Data\Entity\Order $order
	 * @return \Df\C1\Cml2\Import\Data\Collection\Order\Items
	 */
	public static function i(\Df\Xml\X $e, \Df\C1\Cml2\Import\Data\Entity\Order $order) {
		return new self(array(self::$P__E => $e, self::$P__ENTITY_ORDER => $order));
	}
}