<?php
namespace Df\C1\Cml2\Import\Data\Collection;
use Df\C1\Cml2\Import\Data\Entity\Order;
class Orders extends \Df\C1\Cml2\Import\Data\Collection {
	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return Order::class;}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemPath()
	 * @return string|string[]
	 */
	protected function itemPath() {return '/КоммерческаяИнформация/Документ';}

	/**
	 * @used-by \Df\C1\Cml2\Action\Orders\Import::getOrders()
	 * @param \Df\Xml\X $e
	 * @return self
	 */
	public static function i(\Df\Xml\X $e) {return new self([self::$P__E => $e]);}
}