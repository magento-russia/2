<?php
namespace Df\C1\Cml2\Import\Processor\Order;
use \Df\C1\Cml2\Import\Data\Entity\Order as EntityOrder;
use Df\C1\Cml2\Import\Data\Entity\Order\Item as EntityOrderItem;
abstract class Item extends \Df\C1\Cml2\Import\Processor {
	/**
	 * @override
	 * @return EntityOrderItem
	 */
	protected function getEntity() {return parent::getEntity();}

	/** @return EntityOrder */
	protected function getEntityOrder() {return $this->cfg(self::$P__ENTITY_ORDER);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__ENTITY, EntityOrderItem::class)
			->_prop(self::$P__ENTITY_ORDER, EntityOrder::class)
		;
	}
	/** @var string */
	private static $P__ENTITY_ORDER = 'entity_order';

	/**
	 * @used-by \Df\C1\Cml2\Import\Processor\Order::orderItemsProcess()
	 * @param string $class
	 * @param EntityOrderItem $orderItem
	 * @param EntityOrder $order
	 * @return self
	 */
	public static function ic($class, EntityOrderItem $orderItem, EntityOrder $order) {return
		df_ic($class, __CLASS__, [
			self::$P__ENTITY => $orderItem, self::$P__ENTITY_ORDER => $order
		]);
	}
}