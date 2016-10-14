<?php
/**
 * @since Magento CE 1.8.0.0
 */
class Df_Catalog_Helper_Product_Url_Rewrite extends Mage_Catalog_Helper_Product_Url_Rewrite {
	/**
	 * @override
	 * @param Varien_Db_Select $select
	 * @param int $storeId
	 * @return $this|Mage_Catalog_Helper_Product_Url_Rewrite_Interface
	 */
	public function joinTableToSelect(Varien_Db_Select $select, $storeId) {
		$select->joinLeft(
			array('url_rewrite' => rm_table(Df_Catalog_Model_Resource_Url::TABLE))
			,df_cc(
				'url_rewrite.product_id = main_table.entity_id AND url_rewrite.is_system = 1 AND '
				, rm_quote_into(
					/**
					 * НАЧАЛО ЗАПЛАТКИ
					 * Повторяем заплатку из метода
					 * @see Df_Sitemap_Model_Resource_Catalog_Product::getCollectionDf()
					 */
					sprintf(
						'(url_rewrite.category_id IS %s) AND (url_rewrite.store_id = ?) AND '
						,df_cfg()->seo()->urls()->getAddCategoryToProductUrl($storeId)
						? 'NOT NULL'
						: 'NULL'
					)
					// КОНЕЦ ЗАПЛАТКИ
					, (int)$storeId
				)
				, $this->_connection->prepareSqlCondition(
					'url_rewrite.id_path', array('like' => 'product/%')
				)
			)
			,array('request_path' => 'url_rewrite.request_path')
		);
		return $this;
	}
}