<?php
/**
 * Cообщение:		«core_collection_abstract_load_before»
 * Источник:		Mage_Core_Model_Resource_Db_Collection_Abstract::_beforeLoad()
 * [code]
		Mage::dispatchEvent('core_collection_abstract_load_after', array('collection' => $this));
		if ($this->_eventPrefix && $this->_eventObject) {
			Mage::dispatchEvent($this->_eventPrefix.'_load_after', array(
				$this->_eventObject => $this
			));
		}
 * [/code]
 */
class Df_Core_Model_Event_Core_Collection_Abstract_LoadAfter extends Df_Core_Model_Event {
	/** @return Mage_Core_Model_Resource_Db_Collection_Abstract|Mage_Core_Model_Mysql4_Collection_Abstract */
	public function getCollection() {
		/** @var Mage_Core_Model_Resource_Db_Collection_Abstract|Mage_Core_Model_Mysql4_Collection_Abstract $result */
		$result =
			$this->getEventParam(self::EVENT_PARAM__COLLECTION)
		;
		df()->assert()->resourceDbCollectionAbstract($result);
		return $result;
	}

	/** @return string */
	protected function getExpectedEventSuffix() {
		return self::EXPECTED_EVENT_SUFFIX;
	}

	const _CLASS = __CLASS__;
	const EVENT_PARAM__COLLECTION = 'collection';
	const EXPECTED_EVENT_SUFFIX = '_load_after';
}