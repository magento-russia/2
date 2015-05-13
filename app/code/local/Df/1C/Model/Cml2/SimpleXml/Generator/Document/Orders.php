<?php
class Df_1C_Model_Cml2_SimpleXml_Generator_Document_Orders
	extends Df_1C_Model_Cml2_SimpleXml_Generator_Document {
	/**
	 * @override
	 * @return Df_Varien_Simplexml_Element
	 */
	protected function createElement() {
		/** @var Df_Varien_Simplexml_Element $result */
		$result = parent::createElement();
		foreach ($this->getOrders() as $order) {
			/** @var Df_Sales_Model_Order $order */
			Df_1C_Model_Cml2_Export_Processor_Order::i($order, $result)->process();
		}
		return $result;
	}

	/** @return Df_Sales_Model_Resource_Order_Collection */
	private function getOrders() {
		return $this->cfg(self::P__ORDERS);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__ORDERS, Df_Sales_Model_Resource_Order_Collection::_CLASS);
	}
	const _CLASS = __CLASS__;
	const P__ORDERS = 'orders';
	/**
	 * @static
	 * @param Df_Sales_Model_Resource_Order_Collection $orders
	 * @return Df_1C_Model_Cml2_SimpleXml_Generator_Document_Orders
	 */
	public static function _i2(Df_Sales_Model_Resource_Order_Collection $orders) {
		return new self(array(self::P__ORDERS => $orders));
	}
}