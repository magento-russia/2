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
	/** @return Mage_Sales_Model_Order|null */
	public function getOrder() {
		/** @var Mage_Sales_Model_Order|null $result */
		$result = $this->getOrderStatusHistory()->getOrder();
		/**
		 * $this->getOrderStatusHistory()->getOrder() вернёт null
		 * в сценарии "отправить письмо-оповещение", а также в некоторых других
		 */
		if (is_null($result)) {
			$result = Mage::registry('current_order');
		}
		if (!is_null($result)) {
			df_assert($result instanceof Mage_Sales_Model_Order);
		}
		return $result;
	}

	/** @return Mage_Sales_Model_Order_Status_History */
	public function getOrderStatusHistory() {
		/** @var Mage_Sales_Model_Order_Status_History $result */
		$result = $this->getEventParam(self::EVENT_PARAM__DATA_OBJECT);
		df_assert($result instanceof Mage_Sales_Model_Order_Status_History);
		return $result;
	}

	/** @return string */
	protected function getExpectedEventSuffix() {
		return self::EXPECTED_EVENT_SUFFIX;
	}

	const _CLASS = __CLASS__;
	const EVENT_PARAM__DATA_OBJECT = 'data_object';
	const EXPECTED_EVENT_SUFFIX = '_save_before';
}