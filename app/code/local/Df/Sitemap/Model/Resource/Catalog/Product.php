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
		static $patchNeeded;
		if (!isset($patchNeeded)) {
			$patchNeeded =
					df_enabled(Df_Core_Feature::SEO, $storeId)
				&&
					df_cfg()->seo()->urls()->getFixAddCategoryToProductUrl()
				&&
					/**
					 * Для Magento CE 1.8.0.0 и более свежих версий
					 * обработка вынесена в класс
					 * @see Df_Catalog_Helper_Product_Url_Rewrite
					 * Код ниже для Magento CE 1.8.0.0 не подходит:
					 * @link http://magento-forum.ru/topic/4038/
					 */
					df_magento_version('1.8.0.0', '<')
			;
		}
		return
			$patchNeeded
			? $this->getCollectionDf($storeId)
			: parent::getCollection($storeId)
		;
	}

	/**
	 * @param int $storeId
	 * @return array(int => Varien_Object)
	 */
	private function getCollectionDf($storeId) {
		$products = array();
		$store = Mage::app()->getStore($storeId);
		/* @var $store Mage_Core_Model_Store */
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
			, rm_quote_into('ur.store_id=?', $store->getId())
			, rm_quote_into('ur.is_system=?', 1)
		);
		$this->_select = $this->_getWriteAdapter()->select()
			->from(array('e' => $this->getMainTable()), array($this->getIdFieldName()))
			->join(
				array('w' => rm_table('catalog/product_website'))
				,'e.entity_id=w.product_id'
				,array()
			)
			->where('w.website_id=?', $store->getWebsiteId())
			->joinLeft(
				array('ur' => rm_table('core/url_rewrite'))
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
		while ($row = $query->fetch()) {
			$product = $this->_prepareProduct($row);
			$products[$product->getId()] = $product;
		}
		return $products;
	}
}