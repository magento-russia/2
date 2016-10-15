<?php
/**
 * Cообщение:		«catalog_product_collection_load_before»
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
 * Назначение:		Позволяет выполнить дополнительную обработку коллекции товаров
 * 					перед её загрузкой
 */
class Df_Catalog_Model_Event_Product_Collection_Load_Before extends Df_Core_Model_Event {
	/** @return Df_Catalog_Model_Resource_Product_Collection|Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection */
	public function getCollection() {return $this->getEventParam('collection');}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedEventPrefix() {return 'catalog_product_collection_load_before';}

	/**
	 * @used-by Df_AccessControl_Observer::catalog_product_collection_load_before()
	 * @used-by Df_AccessControl_Model_Handler_Catalog_Product_Collection_ExcludeForbiddenProducts::getEventClass()
	 */

}