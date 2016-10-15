<?php
/**
 * Cообщение:		«core_collection_abstract_load_before»
 * Источник:		Mage_Core_Model_Resource_Db_Collection_Abstract::_beforeLoad()
 * [code]
		parent::_beforeLoad();
		Mage::dispatchEvent('core_collection_abstract_load_before', array('collection' => $this));
		if ($this->_eventPrefix && $this->_eventObject) {
			Mage::dispatchEvent($this->_eventPrefix.'_load_before', array(
				$this->_eventObject => $this
			));
		}
 * [/code]
 */
class Df_Core_Model_Event_Core_Collection_Abstract_LoadBefore extends Df_Core_Model_Event {
	/** @return Mage_Core_Model_Resource_Db_Collection_Abstract|Mage_Core_Model_Mysql4_Collection_Abstract */
	public function getCollection() {return $this->getEventParam('collection');}

	/** @return string */
	protected function getExpectedEventSuffix() {return '_load_before';}

	/**
	 * @used-by Df_Directory_Observer::core_collection_abstract_load_before()
	 * @used-by Df_Directory_Model_Handler_OrderRegions::getEventClass()
	 * @used-by Df_Reports_Observer::core_collection_abstract_load_before()
	 * @used-by Df_Reports_Model_Handler_GroupResultsByWeek_PrepareCollection::getEventClass()
	 * @used-by Df_Sales_Observer::sales_order_grid_collection_load_before()
	 */

}