<?php
class Df_1C_Cml2_Export_Document_Orders extends \Df\Xml\Generator\Document {
	/**
	 * @override
	 * @return \Df\Xml\X
	 */
	protected function createElement() {
		/** @var \Df\Xml\X $result */
		$result = parent::createElement();
		foreach ($this->getOrders() as $order) {
			/** @var Df_Sales_Model_Order $order */
			Df_1C_Cml2_Export_Processor_Sale_Order::i($order, $result)->process();
		}
		return $result;
	}

	/** @return Df_1C_Cml2_Export_DocumentMixin */
	protected function createMixin() {return Df_1C_Cml2_Export_DocumentMixin::i($this);}

	/** @return Df_Sales_Model_Resource_Order_Collection */
	private function getOrders() {return $this->cfg(self::$P__ORDERS);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__ORDERS, Df_Sales_Model_Resource_Order_Collection::class);
	}
	/** @var string */
	private static $P__ORDERS = 'orders';
	/**
	 * @used-by Df_1C_Cml2_Action_Orders_Export::createDocument()
	 * @static
	 * @param Df_Sales_Model_Resource_Order_Collection $orders
	 * @return Df_1C_Cml2_Export_Document_Orders
	 */
	public static function i(Df_Sales_Model_Resource_Order_Collection $orders) {
		return new self(array(self::$P__ORDERS => $orders));
	}
}