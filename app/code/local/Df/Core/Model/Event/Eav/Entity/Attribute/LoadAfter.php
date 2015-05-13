<?php
/**
 * Cообщение:		«eav_entity_attribute_load_after»
 * Источник:		Mage_Core_Model_Abstract::_afterLoad()
 * [code]
		Mage::dispatchEvent($this->_eventPrefix.'_load_after', $this->_getEventData());
 * [/code]
 */
class Df_Core_Model_Event_Eav_Entity_Attribute_LoadAfter extends Df_Core_Model_Event {
	/** @return Mage_Eav_Model_Entity_Attribute */
	public function getAttribute() {return $this->getEventParam(self::EVENT_PARAM__ATTRIBUTE);}

	/** @return string */
	protected function getExpectedEventSuffix() {return self::EXPECTED_EVENT_SUFFIX;}

	const _CLASS = __CLASS__;
	const EVENT_PARAM__ATTRIBUTE = 'attribute';
	const EXPECTED_EVENT_SUFFIX = 'entity_attribute_load_after';
}