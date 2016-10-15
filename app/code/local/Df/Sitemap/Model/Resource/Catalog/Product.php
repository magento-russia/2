<?php
class Df_Sitemap_Model_Resource_Catalog_Product extends Mage_Sitemap_Model_Mysql4_Catalog_Product {
	/**
	 * Цель перекрытия —
	 * заплатка для перенаправления посетителей на «правильный», канонический адрес товара.
	 * Эта заплатка работает только для Magento CE версий ниже 1.8.
	 * Для Magento CE 1.8.0.0 и более свежих версий
	 * обработка вынесена в класс
	 * @see Df_Catalog_Helper_Product_Url_Rewrite
	 *
	 * @override
	 * @param int $storeId
	 * @return array(int => Varien_Object)
	 */
	public function getCollection($storeId) {
		/** @var bool $patchNeeded */
		static $patchNeeded; if (is_null($patchNeeded)) {$patchNeeded =
			df_cfg()->seo()->urls()->getFixAddCategoryToProductUrl()
			/**
			 * Для Magento CE 1.8.0.0 и более свежих версий
			 * обработка вынесена в класс @see Df_Catalog_Helper_Product_Url_Rewrite
			 * Код ниже для Magento CE 1.8.0.0 не подходит:
			 * http://magento-forum.ru/topic/4038/
			 */
			&& df_magento_version('1.8.0.0', '<')
		;}
		return $patchNeeded ? $this->getCollectionDf($storeId) : parent::getCollection($storeId);
	}

	/**
	 * @param int $storeId
	 * @return array(int => Varien_Object)
	 */
	private function getCollectionDf($storeId) {
		$products = array();
		/* @var Df_Core_Model_StoreM $store */
		$store = df_store($storeId);
		if (!$store) {
			return false;
		}
		/** @var string $categoryCondition */
		$categoryCondition =
			df_cfg()->seo()->urls()->getAddCategoryToProductUrl($storeId)
			? 'ur.category_id IS NOT null'
			: 'ur.category_id IS null'
		;
		$urCondions = array(
			'e.entity_id=ur.product_id'
			, $categoryCondition
			, df_db_quote_into('ur.store_id=?', $store->getId())
			, df_db_quote_into('ur.is_system=?', 1)
		);
		$this->_select = $this->_getWriteAdapter()->select()
			->from(array('e' => $this->getMainTable()), $this->getIdFieldName())
			->join(
				array('w' => df_table('catalog/product_website'))
				,'e.entity_id=w.product_id'
				,null
			)
			->where('w.website_id=?', $store->getWebsiteId())
			->joinLeft(
				array('ur' => df_table(Df_Catalog_Model_Resource_Url::TABLE))
				,implode(' AND ', $urCondions),array('url' => 'request_path')
			)
		;
		$this->_addFilter(
			$storeId
			, 'visibility'
			, df_mage()->catalog()->product()->visibility()->getVisibleInSiteIds()
			, 'in'
		);
		$this->_addFilter(
			$storeId
			,'status'
			,df_mage()->catalog()->product()->statusSingleton()->getVisibleStatusIds()
			,'in'
		);
		$query = $this->_getWriteAdapter()->query($this->_select);
		/** @noinspection PhpAssignmentInConditionInspection */
		while ($row = $query->fetch()) {
			$product = $this->_prepareProduct($row);
			$products[$product->getId()] = $product;
		}
		return $products;
	}

	/**
	 * 2015-02-09
	 * Возвращаем объект-одиночку именно таким способом,
	 * потому что наш класс перекрывает посредством <rewrite> системный класс,
	 * и мы хотим, чтобы вызов @see Mage::getResourceSingleton() ядром Magento
	 * возвращал тот же объект, что и наш метод @see s(),
	 * сохраняя тем самым объект одиночкой (это важно, например, для производительности:
	 * сохраняя объект одиночкой — мы сохраняем его кэш между всеми пользователями объекта).
	 * @return Df_Sitemap_Model_Resource_Catalog_Product
	 */
	public static function s() {return Mage::getResourceSingleton('sitemap/catalog_product');}
}