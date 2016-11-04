<?php
namespace Df\C1\Cml2\Import\Processor\Order;
abstract class Item extends \Df\C1\Cml2\Import\Processor {
	/**
	 * @override
	 * @return \Df\C1\Cml2\Import\Data\Entity\Order\Item
	 */
	protected function getEntity() {return parent::getEntity();}

	/** @return \Df\C1\Cml2\Import\Data\Entity\Order */
	protected function getEntityOrder() {return $this->cfg(self::$P__ENTITY_ORDER);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__ENTITY, \Df\C1\Cml2\Import\Data\Entity\Order\Item::class)
			->_prop(self::$P__ENTITY_ORDER, \Df\C1\Cml2\Import\Data\Entity\Order::class)
		;
	}
	/** @var string */
	private static $P__ENTITY_ORDER = 'entity_order';

	/**
	 * @used-by \Df\C1\Cml2\Import\Processor\Order::orderItemsProcess()
	 * @param string $class
	 * @param \Df\C1\Cml2\Import\Data\Entity\Order\Item $orderItem
	 * @param \Df\C1\Cml2\Import\Data\Entity\Order $order
	 * @return \Df\C1\Cml2\Import\Processor\Order\Item
	 */
	public static function ic(
		$class
		, \Df\C1\Cml2\Import\Data\Entity\Order\Item $orderItem
		, \Df\C1\Cml2\Import\Data\Entity\Order $order
	) {
		return df_ic($class, __CLASS__, array(
			self::$P__ENTITY => $orderItem, self::$P__ENTITY_ORDER => $order
		));
	}
}