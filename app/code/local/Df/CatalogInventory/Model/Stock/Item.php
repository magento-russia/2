<?php
class Df_CatalogInventory_Model_Stock_Item extends Mage_CatalogInventory_Model_Stock_Item {
	/**
	 * Исправляем дефект метода Mage_CatalogInventory_Model_Indexer_Stock::_registerStockItemSaveEvent()
	 * Этот метод запускает переиндексацию складских остатков, если с полученным им объектом
	 * Df_CatalogInventory_Model_Stock_Item не связан объект Mage_Catalog_Model_Product.
	 *
	 * Однако для проверки такой связи Mage_CatalogInventory_Model_Indexer_Stock::_registerStockItemSaveEvent()
	 * использует метод Mage_CatalogInventory_Model_Stock_Item::getProduct().
	 *
	 * Метод getProduct() отсутствует в классе Mage_CatalogInventory_Model_Indexer_Stock
	 * и поле «product» тоже отсутствует,
	 * поэтому вызов Mage_CatalogInventory_Model_Stock_Item::getProduct() возвращает null,
	 * и Mage_CatalogInventory_Model_Indexer_Stock::_registerStockItemSaveEvent()
	 * запускает ненужную (дублирующую) переиндексацию
	 * @return Mage_Catalog_Model_Product|null
	 */
	public function getProduct() {
		/**
		 * Учитываем ситуацию, что в будущих версиях Magento метод parent::getProduct()
		 * может вернуть непустое значение.
		 * Внимание: parent::getDataUsingMethod('product') ошибочно, ибо приводит к рекурсии!
		 */
		/** @var Mage_Catalog_Model_Product|null $result */
		$result =  parent::getProduct();
		/** @var bool $optimizationNeeded */
		static $optimizationNeeded;
		if (is_null($optimizationNeeded)) {
			$optimizationNeeded =
				df_cfg()->admin()->optimization()->getFixDoubleStockReindexingOnProductSave()
			;
		}
		if ($optimizationNeeded && !$result && isset($this->_productInstance)) {
			$result = $this->_productInstance;
		}
		return $result;
	}

	/** @return Df_CatalogInventory_Model_Stock_Item */
	public static function i() {return new self;}
}