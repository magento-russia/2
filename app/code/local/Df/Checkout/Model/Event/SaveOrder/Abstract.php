<?php
abstract class Df_Checkout_Model_Event_SaveOrder_Abstract extends Df_Core_Model_Event {
	/** @return Df_Sales_Model_Order */
	public function getOrder() {return $this->getEventParam('order');}
	/** @used-by Df_Checkout_Model_Handler_SaveOrderComment::getEventClass() */
	const _C = __CLASS__;
}