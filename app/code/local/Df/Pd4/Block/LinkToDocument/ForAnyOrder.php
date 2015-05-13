<?php
class Df_Pd4_Block_LinkToDocument_ForAnyOrder extends Df_Pd4_Block_LinkToDocument {
	/**
	 * @override
	 * @return Mage_Sales_Model_Order
	 */
	public function getOrder() {return $this->_order;}

	/**
	 * @param Mage_Sales_Model_Order $order
	 * @return Df_Pd4_Block_LinkToDocument_ForAnyOrder
	 */
	public function setOrder(Mage_Sales_Model_Order $order) {
		$this->_order = $order;
		return $this;
	}
	/** @var Mage_Sales_Model_Order */
	private $_order;

	/**
	 * @override
	 * @return string|null
	 */
	protected function getDefaultTemplate() {return 'df/pd4/link_to_document/for_any_order.phtml';}

	const _CLASS = __CLASS__;

	/** @return Df_Pd4_Block_LinkToDocument_ForAnyOrder */
	public static function i() {return df_block(__CLASS__);}
}