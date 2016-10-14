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
	public function getCollection() {return $this->getEventParam('collection');}

	/** @return string */
	protected function getExpectedEventSuffix() {return '_load_after';}

	/**
	 * @used-by Df_Customer_Observer::form_attribute_collection__load_after()
	 * @used-by Df_Customer_Model_Handler_FormAttributeCollection_AdjustApplicability::getEventClass()
	 * @used-by Df_Directory_Observer::core_collection_abstract_load_after()
	 * @used-by Df_Directory_Model_Handler_ProcessRegionsAfterLoading::getEventClass()
	 */
	const _C = __CLASS__;
}