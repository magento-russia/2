<?php
/**
 * Cообщение:		«eav_entity_attribute_load_after»
 * Источник:		Mage_Core_Model_Abstract::_afterLoad()
 * [code]
		Mage::dispatchEvent($this->_eventPrefix.'_load_after', $this->_getEventData());
 * [/code]
 * @method Mage_Customer_Model_Attribute getAttribute()
 */
class Df_Customer_Model_Event_Attribute_LoadAfter
	extends Df_Core_Model_Event_Eav_Entity_Attribute_LoadAfter {
	/** @return string */
	protected function getExpectedEventSuffix() {
		return self::EXPECTED_EVENT_SUFFIX;
	}

	const _CLASS = __CLASS__;
	const EXPECTED_EVENT_SUFFIX = 'customer_entity_attribute_load_after';
}