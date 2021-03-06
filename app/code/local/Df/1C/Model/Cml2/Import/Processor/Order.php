<?php
class Df_1C_Model_Cml2_Import_Processor_Order extends Df_1C_Model_Cml2_Import_Processor {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		/**
		 * Обновляем в Magento заказ
		 * на основании пришедших из 1С:Управление торговлей данных
		 */
		if (is_null($this->getEntity()->getOrder())) {
			rm_1c_log(
				'Отказываемся импортировать заказ №%s, '
				.'потому что этот заказ отсутствует в Magento'
				,$this->getEntity()->getIncrementId()
			);
		}
		else {
			/**
			 * Итак, пришедший из 1С:Управление торговлей заказ найден в магазине.
			 * Обновляем заказ в магазине.
			 * Обратите внимание, что при ручном редактировании заказа
			 * из административной части Magento система:
			 * 1) отменяет подлежащий редактированию заказ
			 * 2) создаёт его копию — новый заказ
			 * 3) привязывает копию к подлежащему редактированию заказу
			 * 4) редактирует и сохраняет копию.
			 *
			 * Мы так делать не будем,
			 * вместо этого будем менять подлежащий редактированию заказ напрямую.
			 */

			/**
			 * Исходим из предпосылки, что в разным простым строкам заказа в Magento
			 * непременно соответствуют разные товары
			 *
			 * @todo ЭТО НЕПРАВДА!
			 * @todo В заказе один и тот же простой товар с настраиваемыми вариантами
			 * @todo может присутствовать в разных строках:
			 * @todo каждой строке будет соответствовать свой настраиваемый вариант.
			 *
			 *
			 * В импортирумых данных разным строкам заказа тоже соответствуют разные товары
			 * благодаря применению шаблона composite:
			 * @see Df_1C_Model_Cml2_Import_Data_Entity_Order_Item_Composite
			 *
			 * Поэтому путём сравнения множества идентификаторов товаров в заказе Magento
			 * с множеством идентификатором товаров импортированной версии того же самого заказа, * мы увидим:
			 * а) какие строки заказа надо удалить
			 * б) какие строки заказа надо изменить
			 * в) какие строки заказа надо добавить
			 */
//			$this->orderItemsUpdate();
//			$this->orderItemsDelete();
//			$this->orderItemsAdd();
		}
	}

	/**
	 * @override
	 * @return Df_1C_Model_Cml2_Import_Data_Entity_Order
	 */
	protected function getEntity() {
		return parent::getEntity();
	}

	/** @return Mage_Sales_Model_Order_Item[] */
	private function getMapFromProductIdToSimpleOrderItem() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Sales_Model_Order_Item[] $result */
			$result = array();
			foreach ($this->getEntity()->getOrder()->getAllItems() as $orderItem) {
				/** @var Mage_Sales_Model_Order_Item $orderItem */
				/**
				 * Собираем идентификаторы только простых товаров.
				 */
				if (Mage_Catalog_Model_Product_Type::TYPE_SIMPLE === $orderItem->getProductType()) {
					/** @var int $productId */
					$productId = rm_nat($orderItem->getProductId());
					df_assert(is_null(df_a($result, $productId)));
					$result[$productId] = $orderItem;
				}
			}
			df_result_array($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return int[] */
	private function getProductIdsFrom1COrder() {
		if (!isset($this->{__METHOD__})) {
			/** @var int[] $result */
			$result = array();
			foreach ($this->getEntity()->getItems() as $entityOrderItem) {
				/** @var Df_1C_Model_Cml2_Import_Data_Entity_Order_Item $entityOrderItem */
				$result[]= $entityOrderItem->getProduct()->getId();
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Возвращает идентификаторы только простых товаров
	 * @return int[]
	 */
	private function getProductIdsFromMagentoOrder() {
		if (!isset($this->{__METHOD__})) {
			/** @var int[] $result */
			$result = array();
			foreach ($this->getEntity()->getOrder()->getAllItems() as $orderItem) {
				/** @var Mage_Sales_Model_Order_Item $orderItem */
				/**
				 * Собираем идентификаторы только простых товаров.
				 */
				if (Mage_Catalog_Model_Product_Type::TYPE_SIMPLE === $orderItem->getProductType()) {
					$result[]= $orderItem->getProductId();
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return int[] */
	private function getProductIdsToAdd() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				array_diff($this->getProductIdsFrom1COrder(), $this->getProductIdsFromMagentoOrder())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return int[] */
	private function getProductIdsToDelete() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				array_diff($this->getProductIdsFromMagentoOrder(), $this->getProductIdsFrom1COrder())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return int[] */
	private function getProductIdsToUpdate() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				array_intersect($this->getProductIdsFromMagentoOrder(), $this->getProductIdsFrom1COrder())
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param int $productId
	 * @return Mage_Sales_Model_Order_Item
	 */
	private function getSimpleOrderItemByProductId($productId) {
		df_param_integer($productId, 0);
		df_param_between($productId, 0, 1);
		/** @var Mage_Sales_Model_Order_Item $result */
		$result = df_a($this->getMapFromProductIdToSimpleOrderItem(), $productId);
		df_assert($result instanceof Mage_Sales_Model_Order_Item);
		return $result;
	}

	/** @return Df_1C_Model_Cml2_Import_Processor_Order */
	private function orderItemsAdd() {
		$this->orderItemsProcess(
			$this->getProductIdsToAdd(), Df_1C_Model_Cml2_Import_Processor_Order_Item_Add::_CLASS
		);
		return $this;
	}

	/** @return Df_1C_Model_Cml2_Import_Processor_Order */
	private function orderItemsDelete() {
//		$this
//			->orderItemsProcess(
//				$this->getProductIdsToDelete()
//				,
//				Df_1C_Model_Cml2_Import_Processor_Order_Item_Delete::_CLASS
//			)
//		;
		foreach ($this->getProductIdsToDelete() as $productId) {
			/** @var int $productId */
			df_assert_integer($productId);
			df_assert_gt0($productId);
			/** @var Mage_Sales_Model_Order_Item $orderItem */
			$orderItem = $this->getSimpleOrderItemByProductId($productId);
			/** @var Df_1C_Model_Cml2_Import_Processor_Order_Item $processor */
//			$processor =
//				df_model(
//					$processorClassMf
//					,
//					array(
//						Df_1C_Model_Cml2_Import_Processor_Order_Item_Add
//							::P__ENTITY_ORDER => $this->getEntity()
//						,
//						Df_1C_Model_Cml2_Import_Processor_Order_Item_Add::P__ENTITY =>
//							$this->getEntity()->getItems()->getItemByProductId($productId)
//					)
//				)
//			;
//
//			df_assert($processor instanceof Df_1C_Model_Cml2_Import_Processor_Order_Item);
//
//			$processor->process();
		}
		return $this;
	}

	/**
	 * @param int[] $productIds
	 * @param string $processorClass
	 * @return Df_1C_Model_Cml2_Import_Processor_Order
	 */
	private function orderItemsProcess(array $productIds, $processorClass) {
		foreach ($productIds as $productId) {
			/** @var int $productId */
			df_assert_integer($productId);
			df_assert_gt0($productId);
			/** @var Df_1C_Model_Cml2_Import_Processor_Order_Item $processor */
			$processor =
				df_model(
					$processorClass
					,array(
						Df_1C_Model_Cml2_Import_Processor_Order_Item_Add
							::P__ENTITY_ORDER => $this->getEntity()
						,Df_1C_Model_Cml2_Import_Processor_Order_Item_Add::P__ENTITY =>
							$this->getEntity()->getItems()->getItemByProductId($productId)
					)
				)
			;
			df_assert($processor instanceof Df_1C_Model_Cml2_Import_Processor_Order_Item);
			$processor->process();
		}
		return $this;
	}

	/** @return Df_1C_Model_Cml2_Import_Processor_Order */
	private function orderItemsUpdate() {
		$this->orderItemsProcess(
			$this->getProductIdsToUpdate(), Df_1C_Model_Cml2_Import_Processor_Order_Item_Update::_CLASS
		);
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__ENTITY, Df_1C_Model_Cml2_Import_Data_Entity_Order::_CLASS);
	}
	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param Df_1C_Model_Cml2_Import_Data_Entity_Order $order
	 * @return Df_1C_Model_Cml2_Import_Processor_Order
	 */
	public static function i(Df_1C_Model_Cml2_Import_Data_Entity_Order $order) {
		return new self(array(self::P__ENTITY => $order));
	}
}