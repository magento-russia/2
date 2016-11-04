<?php
namespace Df\C1\Cml2\Export\Document;
use Df_Sales_Model_Resource_Order_Collection as OC;
class Orders extends \Df\Xml\Generator\Document {
	/**
	 * @override
	 * @return \Df\Xml\X
	 */
	protected function createElement() {
		/** @var \Df\Xml\X $result */
		$result = parent::createElement();
		foreach ($this->getOrders() as $order) {
			/** @var \Df_Sales_Model_Order $order */
			\Df\C1\Cml2\Export\Processor\Sale\Order::i($order, $result)->process();
		}
		return $result;
	}

	/** @return \Df\C1\Cml2\Export\DocumentMixin */
	protected function createMixin() {return \Df\C1\Cml2\Export\DocumentMixin::i($this);}

	/** @return \Df_Sales_Model_Resource_Order_Collection */
	private function getOrders() {return $this->cfg(self::$P__ORDERS);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__ORDERS, OC::class);
	}
	/** @var string */
	private static $P__ORDERS = 'orders';
	/**
	 * @used-by \Df\C1\Cml2\Action\Orders\Export::createDocument()
	 * @static
	 * @param OC $orders
	 * @return \Df\C1\Cml2\Export\Document\Orders
	 */
	public static function i(OC $orders) {return new self([self::$P__ORDERS => $orders]);}
}