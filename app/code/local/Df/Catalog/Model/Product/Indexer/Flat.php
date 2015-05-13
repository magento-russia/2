<?php
class Df_Catalog_Model_Product_Indexer_Flat extends Mage_Catalog_Model_Product_Indexer_Flat {
	/**
	 * @override
	 */
	public function reindexAll() {
		/**
		 * Важно!
		 * Иначе повторная индексация не будет работать в полной мере.
		 * В частности, если мы добавим к прикладному типу товаров новые свойства,
		 * затем перестроим денормализованную таблицу, затем добавим еще свойства,
		 * то вторая перестройка денормализованной таблицы не сработает:
		 * @see Mage_Catalog_Model_Resource_Product_Flat_Indexer::prepareFlatTable():
				if (isset($this->_preparedFlatTables[$storeId])) {
				  return $this;
			  }
		 */
		Mage::unregister('_resource_singleton/' . $this->_getIndexer()->getResourceName());
		parent::reindexAll();
	}
}