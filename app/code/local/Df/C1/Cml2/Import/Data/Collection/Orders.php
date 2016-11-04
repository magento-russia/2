<?php
namespace Df\C1\Cml2\Import\Data\Collection;
class Orders extends \Df\C1\Cml2\Import\Data\Collection {
	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return \Df\C1\Cml2\Import\Data\Entity\Order::class;}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemPath()
	 * @return string|string[]
	 */
	protected function itemPath() {return '/КоммерческаяИнформация/Документ';}

	/**
	 * @used-by \Df\C1\Cml2\Action\Orders\Import::getOrders()
	 * @static
	 * @param \Df\Xml\X $e
	 * @return \Df\C1\Cml2\Import\Data\Collection\Orders
	 */
	public static function i(\Df\Xml\X $e) {return new self(array(self::$P__E => $e));}
}