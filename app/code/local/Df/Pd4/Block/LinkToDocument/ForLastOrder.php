<?php
class Df_Pd4_Block_LinkToDocument_ForLastOrder extends Df_Pd4_Block_LinkToDocument {
	/**
	 * @override
	 * @return Df_Sales_Model_Order
	 */
	protected function getOrder() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Sales_Model_Order::ld($this->getLastOrderId());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string|null
	 */
	protected function getDefaultTemplate() {return 'df/pd4/link_to_document/for_last_order.phtml';}

	/** @return int */
	private function getLastOrderId() {
		return rm_nat(rm_session_checkout()->getDataUsingMethod(
			Df_Checkout_Const::SESSION_PARAM__LAST_ORDER_ID
		));
	}

	const _CLASS = __CLASS__;
}