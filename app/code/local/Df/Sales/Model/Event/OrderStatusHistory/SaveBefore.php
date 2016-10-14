<?php
/**
 * Cообщение:		«sales_order_status_history_save_before»
 * Источник:		Mage_Core_Model_Abstract::_beforeSave()
 * [code]
		Mage::dispatchEvent('model_save_before', array('object'=>$this));
		Mage::dispatchEvent($this->_eventPrefix.'_save_before', $this->_getEventData());
 * [/code]
 */
class Df_Sales_Model_Event_OrderStatusHistory_SaveBefore extends Df_Core_Model_Event {
	/** @return Df_Sales_Model_Order|null */
	public function getOrder() {
		/** @var Df_Sales_Model_Order|null $result */
		$result = $this->getOrderStatusHistory()->getOrder();
		/**
		 * $this->getOrderStatusHistory()->getOrder() вернёт null
		 * в сценарии "отправить письмо-оповещение", а также в некоторых других
		 */
		if (is_null($result)) {
			$result = Mage::registry('current_order');
		}
		if (!is_null($result)) {
			df_assert($result instanceof Df_Sales_Model_Order);
		}
		return $result;
	}

	/** @return Mage_Sales_Model_Order_Status_History */
	public function getOrderStatusHistory() {return $this->getEventParam('data_object');}

	/** @return string */
	protected function getExpectedEventSuffix() {return '_save_before';}

	/**
	 * @used-by Df_Sales_Observer::sales_order_status_history_save_before()
	 * @used-by Df_Sales_Model_Handler_OrderStatusHistory_SetVisibleOnFrontParam::getEventClass()
	 */
	const _C = __CLASS__;
}