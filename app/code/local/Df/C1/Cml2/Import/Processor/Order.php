<?php
namespace Df\C1\Cml2\Import\Processor;
use Df\C1\Cml2\Import\Data\Entity\Order as EntityOrder;
use Df\C1\Cml2\Import\Data\Entity\Order\Item as EntityOrderItem;
use Mage_Catalog_Model_Product_Type as Type;
use Mage_Sales_Model_Order_Item as OI;
/** @method EntityOrder getEntity() */
class Order extends \Df\C1\Cml2\Import\Processor {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		// Обновляем в Magento заказ
		// на основании пришедших из 1С:Управление торговлей данных.
		if (is_null($this->getEntity()->getOrder())) {
			df_c1_log(
				'Отказываемся импортировать заказ №%s, потому что этот заказ отсутствует в Magento.'
				, $this->getEntity()->getIncrementId()
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
			 * @see \Df\C1\Cml2\Import\Data\Entity\Order\Item\Composite
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

	/** @return array(int => OI) */
	private function getMapFromProductIdToSimpleOrderItem() {return dfc($this, function() {
		/** @var array(int => OI) $result */
		$result = [];
		foreach ($this->getEntity()->getOrder()->getAllItems() as $orderItem) {
			/** @var OI $orderItem */
			// собираем идентификаторы только простых товаров
			if (Type::TYPE_SIMPLE === $orderItem->getProductType()) {
				/** @var int $productId */
				$productId = df_nat($orderItem->getProductId());
				df_assert(is_null(dfa($result, $productId)));
				$result[$productId] = $orderItem;
			}
		}
		return $result;
	});}

	/** @return int[] */
	private function getProductIdsFrom1COrder() {return dfc($this, function() {return
		df_map(function(EntityOrderItem $i) {return
			$i->getProduct()->getId()
		;}, $this->getEntity()->getItems())
	;});}

	/**
	 * Возвращает идентификаторы только простых товаров
	 * @return int[]
	 */
	private function getProductIdsFromMagentoOrder() {return dfc($this, function() {return
		array_filter(df_map(function(OI $i) {return
			// Собираем идентификаторы только простых товаров.
			Type::TYPE_SIMPLE === $i->getProductType() ? $i->getProductId() : null
		;}, $this->getEntity()->getOrder()->getAllItems()))
	;});}

	/** @return int[] */
	private function getProductIdsToAdd() {return dfc($this, function() {return
		array_diff($this->getProductIdsFrom1COrder(), $this->getProductIdsFromMagentoOrder())
	;});}

	/** @return int[] */
	private function getProductIdsToDelete() {return dfc($this, function() {return
		array_diff($this->getProductIdsFromMagentoOrder(), $this->getProductIdsFrom1COrder())
	;});}

	/** @return int[] */
	private function getProductIdsToUpdate() {return dfc($this, function() {return
		array_intersect($this->getProductIdsFromMagentoOrder(), $this->getProductIdsFrom1COrder())
	;});}

	/** @return void */
	private function orderItemsAdd() {
		$this->orderItemsProcess($this->getProductIdsToAdd(), Order\Item\Add::class);
	}

	/**
	 * @param int[] $productIds
	 * @param string $processorClass
	 * @return void
	 */
	private function orderItemsProcess(array $productIds, $processorClass) {
		foreach ($productIds as $productId) {
			/** @var int $productId */
			\Df\C1\Cml2\Import\Processor\Order\Item::ic(
				$processorClass
				, $this->getEntity()->getItems()->getItemByProductId($productId)
				, $this->getEntity()
			)->process();
		}
	}

	/** @return void */
	private function orderItemsUpdate() {
		$this->orderItemsProcess(
			$this->getProductIdsToUpdate(), \Df\C1\Cml2\Import\Processor\Order\Item\Update::class
		);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__ENTITY, EntityOrder::class);
	}
	/**
	 * @param EntityOrder $order
	 * @return self
	 */
	public static function i(EntityOrder $order) {return new self([self::$P__ENTITY => $order]);}
}