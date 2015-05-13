<?php
abstract class Df_Checkout_Model_Event_SaveOrder_Abstract extends Df_Core_Model_Event {
	/** @return Mage_Sales_Model_Order */
	public function getOrder() {
		/** @var Mage_Sales_Model_Order $result */
		$result = $this->getEventParam(self::EVENT_PARAM__ORDER);
		df_assert($result instanceof Mage_Sales_Model_Order);
		return $result;
	}
	const _CLASS = __CLASS__;
	const EVENT_PARAM__ORDER = 'order';
}