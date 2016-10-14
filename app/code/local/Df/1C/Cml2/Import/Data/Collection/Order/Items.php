<?php
class Df_1C_Cml2_Import_Data_Collection_Order_Items
	extends Df_1C_Cml2_Import_Data_Collection {
	/**
	 * @param int $productId
	 * @return Df_1C_Cml2_Import_Data_Entity_Order_Item
	 */
	public function getItemByProductId($productId) {
		df_param_integer($productId, 0);
		df_param_between($productId, 0, 1);
		/** @var Df_1C_Cml2_Import_Data_Entity_Order_Item $result */
		$result = dfa($this->getMapFromProductIdToOrderItem(), $productId);
		df_assert($result instanceof Df_1C_Cml2_Import_Data_Entity_Order_Item);
		return $result;
	}

	/**
	 * @override
	 * @see Df_Core_Xml_Parser_Collection::initItems()
	 * @used-by Df_Core_Xml_Parser_Collection::getItems()
	 * @return Df_1C_Cml2_Import_Data_Entity_Order_Item[]
	 */
	protected function initItems() {
		/** @var Df_1C_Cml2_Import_Data_Entity_Order_Item[] $result */
		$result = array();
		/** @var Df_1C_Cml2_Import_Data_Entity_Order_Item[] $mapFromProductsToOrderItems */
		$mapFromProductsToOrderItems = array();
		foreach ($this->getImportEntitiesAsSimpleXMLElementArray() as $e) {
			/** @var Df_Core_Sxe $e */
			/** @var Df_1C_Cml2_Import_Data_Entity_Order_Item $orderItem */
			$orderItem = $this->createItem($e);
			df_assert($orderItem instanceof Df_1C_Cml2_Import_Data_Entity_Order_Item);
			/** @var int $productId */
			$productId = $orderItem->getProduct()->getId();
			df_assert_integer($productId);
			/** @var Df_1C_Cml2_Import_Data_Entity_Order_Item[] $orderItemsForTheProduct */
			$orderItemsForTheProduct = df_nta(dfa($mapFromProductsToOrderItems, $productId));
			$orderItemsForTheProduct[]= $orderItem;
			$mapFromProductsToOrderItems[$productId] = $orderItemsForTheProduct;
		}
		foreach ($mapFromProductsToOrderItems as $orderItemsForTheProduct) {
			/** @var Df_1C_Cml2_Import_Data_Entity_Order_Item[] $orderItemsForTheProduct */
			/** @var int $orderItemsCount */
			$orderItemsCount = count($orderItemsForTheProduct);
			df_assert_gt0($orderItemsCount);
			$result[]=
				1 === $orderItemsCount
				? dfa($orderItemsForTheProduct, 0)
				: Df_1C_Cml2_Import_Data_Entity_Order_Item_Composite::i(
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
	 * @see Df_Core_Xml_Parser_Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return Df_1C_Cml2_Import_Data_Entity_Order_Item::_C;}

	/**
	 * @override
	 * @return array(string = mixed)
	 */
	protected function itemParams() {
		return array(Df_1C_Cml2_Import_Data_Entity_Order_Item::P__ENTITY_ORDER => $this->getEntityOrder());
	}

	/**
	 * @override
	 * @see Df_Core_Xml_Parser_Collection::itemPath()
	 * @return string|string[]
	 */
	protected function itemPath() {return 'Товары/Товар';}

	/** @return Df_1C_Cml2_Import_Data_Entity_Order */
	private function getEntityOrder() {
		return $this->cfg(self::$P__ENTITY_ORDER);
	}

	/** @return array(int => Df_1C_Cml2_Import_Data_Entity_Order_Item) */
	private function getMapFromProductIdToOrderItem() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(int => Df_1C_Cml2_Import_Data_Entity_Order_Item) $result */
			$result = array();
			foreach ($this->getItems() as $entityOrderItem) {
				/** @var Df_1C_Cml2_Import_Data_Entity_Order_Item $entityOrderItem */
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
		$this->_prop(self::$P__ENTITY_ORDER, Df_1C_Cml2_Import_Data_Entity_Order::_C);
	}
	/** @var string */
	private static $P__ENTITY_ORDER = 'entity_order';
	/**
	 * @used-by Df_1C_Cml2_Import_Data_Entity_Order::getItems()
	 * @static
	 * @param Df_Core_Sxe $e
	 * @param Df_1C_Cml2_Import_Data_Entity_Order $order
	 * @return Df_1C_Cml2_Import_Data_Collection_Order_Items
	 */
	public static function i(Df_Core_Sxe $e, Df_1C_Cml2_Import_Data_Entity_Order $order) {
		return new self(array(self::$P__E => $e, self::$P__ENTITY_ORDER => $order));
	}
}