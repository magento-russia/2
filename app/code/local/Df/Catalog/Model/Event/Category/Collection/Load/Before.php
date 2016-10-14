<?php
/**
 * Cообщение:		«catalog_category_collection_load_before»
 *
 * Источник:		Mage_Core_Model_Mysql4_Collection_Abstract::_beforeLoad()
 * 					Mage_Core_Model_Resource_Collection_Abstract::_beforeLoad()
 * [code]
		parent::_beforeLoad();
		Mage::dispatchEvent('core_collection_abstract_load_before', array('collection' => $this));
		if ($this->_eventPrefix && $this->_eventObject) {
			Mage::dispatchEvent($this->_eventPrefix.'_load_before', array(
				$this->_eventObject => $this
			));
		}
 * [/code]
 *
 * Назначение:		Позволяет выполнить дополнительную обработку коллекции товарных разделов
 * 					перед её загрузкой
 */
class Df_Catalog_Model_Event_Category_Collection_Load_Before extends Df_Core_Model_Event {
	/** @return Mage_Catalog_Model_Resource_Category_Collection|Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection */
	public function getCollection() {return $this->getEventParam('category_collection');}
	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedEventPrefix() {return 'catalog_category_collection_load_before';}

	/**
	 * @used-by Df_AccessControl_Observer::catalog_category_collection_load_before()
	 * @used-by Df_AccessControl_Model_Handler_Catalog_Category_Collection_ExcludeForbiddenCategories::getEventClass()
	 */
	const _C = __CLASS__;
}