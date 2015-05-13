<?php
class Df_1C_Model_Cml2_Import_Data_Collection_Order_Items
	extends Df_1C_Model_Cml2_Import_Data_Collection {
	/**
	 * @param int $productId
	 * @return Df_1C_Model_Cml2_Import_Data_Entity_Order_Item
	 */
	public function getItemByProductId($productId) {
		df_param_integer($productId, 0);
		df_param_between($productId, 0, 1);
		/** @var Df_1C_Model_Cml2_Import_Data_Entity_Order_Item $result */
		$result = df_a($this->getMapFromProductIdToOrderItem(), $productId);
		df_assert($result instanceof Df_1C_Model_Cml2_Import_Data_Entity_Order_Item);
		return $result;
	}

	/**
	 * @override
	 * @return Df_1C_Model_Cml2_Import_Data_Entity_Order_Item[]
	 */
	public function getItems() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_1C_Model_Cml2_Import_Data_Entity_Order_Item[] $result */
			$result = new ArrayObject (array());
			/** @var Df_1C_Model_Cml2_Import_Data_Entity_Order_Item[] $mapFromProductsToOrderItems */
			$mapFromProductsToOrderItems = array();
			foreach ($this->getImportEntitiesAsSimpleXMLElementArray() as $entityAsSimpleXMLElement) {
				/** @var Df_Varien_Simplexml_Element $entityAsSimpleXMLElement */
				/** @var Df_1C_Model_Cml2_Import_Data_Entity_Order_Item $orderItem */
				$orderItem = $this->createItemFromSimpleXmlElement($entityAsSimpleXMLElement);
				df_assert($orderItem instanceof Df_1C_Model_Cml2_Import_Data_Entity_Order_Item);
				/** @var int $productId */
				$productId = $orderItem->getProduct()->getId();
				df_assert_integer($productId);
				/** @var Df_1C_Model_Cml2_Import_Data_Entity_Order_Item[] $orderItemsForTheProduct */
				$orderItemsForTheProduct = df_a($mapFromProductsToOrderItems, $productId, array());
				df_assert_array($orderItemsForTheProduct);
				$orderItemsForTheProduct[]= $orderItem;
				$mapFromProductsToOrderItems[$productId] = $orderItemsForTheProduct;
			}
			foreach ($mapFromProductsToOrderItems as $orderItemsForTheProduct) {
				/** @var Df_1C_Model_Cml2_Import_Data_Entity_Order_Item[] $orderItemsForTheProduct */
				/** @var int $orderItemsCount */
				$orderItemsCount = count($orderItemsForTheProduct);
				df_assert_gt0($orderItemsCount);
				if (1 === $orderItemsCount) {
					$result[]= df_a($orderItemsForTheProduct, 0);
				}
				else {
					$result[]=
						Df_1C_Model_Cml2_Import_Data_Entity_Order_Item_Composite::i(
							array(
								Df_1C_Model_Cml2_Import_Data_Entity_Order_Item_Composite
									/**
									 * Намеренно передаём null!
									 */
									::P__SIMPLE_XML => null
								,Df_1C_Model_Cml2_Import_Data_Entity_Order_Item_Composite
									::P__ENTITY_ORDER => $this->getEntityOrder()
								,Df_1C_Model_Cml2_Import_Data_Entity_Order_Item_Composite
									::P__SIMPLE_ITEMS => $orderItemsForTheProduct
							)
						)
					;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getItemClass() {return Df_1C_Model_Cml2_Import_Data_Entity_Order_Item::_CLASS;}

	/**
	 * @override
	 * @return array(string = mixed)
	 */
	protected function getItemParamsAdditional() {
		/** @var array(string = mixed) $result */
		$result = parent::getItemParamsAdditional();
		$result[Df_1C_Model_Cml2_Import_Data_Entity_Order_Item::P__ENTITY_ORDER] =
			$this->getEntityOrder()
		;
		return $result;
	}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getItemsXmlPathAsArray() {return array('Товары', 'Товар');}

	/** @return Df_1C_Model_Cml2_Import_Data_Entity_Order */
	private function getEntityOrder() {
		return $this->cfg(self::P__ENTITY_ORDER);
	}

	/** @return array(int => Df_1C_Model_Cml2_Import_Data_Entity_Order_Item) */
	private function getMapFromProductIdToOrderItem() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(int => Df_1C_Model_Cml2_Import_Data_Entity_Order_Item) $result */
			$result = array();
			foreach ($this->getItems() as $entityOrderItem) {
				/** @var Df_1C_Model_Cml2_Import_Data_Entity_Order_Item $entityOrderItem */
				/** @var int $productId */
				$productId = $entityOrderItem->getProduct()->getId();
				df_assert_gt0($productId);
				df_assert(is_null(df_a($result, $productId)));
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
		$this->_prop(self::P__ENTITY_ORDER, Df_1C_Model_Cml2_Import_Data_Entity_Order::_CLASS);
	}
	const _CLASS = __CLASS__;
	const P__ENTITY_ORDER = 'entity_order';
	/**
	 * @static
	 * @param Df_Varien_Simplexml_Element $element
	 * @param Df_1C_Model_Cml2_Import_Data_Entity_Order $order
	 * @return Df_1C_Model_Cml2_Import_Data_Collection_Order_Items
	 */
	public static function i(
		Df_Varien_Simplexml_Element $element, Df_1C_Model_Cml2_Import_Data_Entity_Order $order
	) {
		return new self(array(self::P__SIMPLE_XML => $element, self::P__ENTITY_ORDER => $order));
	}
}