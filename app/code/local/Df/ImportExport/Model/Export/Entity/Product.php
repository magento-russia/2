<?php
class Df_ImportExport_Model_Export_Entity_Product
	extends Mage_ImportExport_Model_Export_Entity_Product {
	/**
	 * Цель перекрытия —
	 * устранение сбоя «No valid data sent» при экспорте товаров
	 * «Undefined offset in app/code/core/Mage/ImportExport/Model/Export/Entity/Product.php on line 873»
	 * http://magento-forum.ru/topic/3835/
	 * http://www.magentocommerce.com/bug-tracking/issue?issue=15022
	 * @override
	 * @param int[] $productIds
	 * @return array(int => array(string => string))
	 */
	protected function _prepareCatalogInventory(array $productIds) {
		return parent::_prepareCatalogInventory($productIds) + array_fill_keys($productIds, array());
	}

	/**
	 * @override
	 * Устраняет сбой «No valid data sent» при экспорте товаров
	 * http://magento-forum.ru/topic/3711/
	 * http://stackoverflow.com/questions/11886249/magento-community-1-7-0-2-export-products-csv
	 * @param array $dataRow
	 * @param array $rowCategories
	 * @param int $productId
	 * @return bool
	 */
	protected function _updateDataWithCategoryColumns(&$dataRow, &$rowCategories, $productId) {
		return
			!df_a($rowCategories, $productId)
			? false
			: parent::_updateDataWithCategoryColumns($dataRow, $rowCategories, $productId)
		;
	}
}


