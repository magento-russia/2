<?php
class Df_PageCache_Model_Processor_Product extends Df_PageCache_Model_Processor_Default
{
	/**
	 * Key for saving product id in metadata
	 */
	const METADATA_PRODUCT_ID = 'current_product_id';

	/**
	 * Prepare response body before caching
	 *
	 * @param Zend_Controller_Response_Http $response
	 * @return string
	 */
	public function prepareContent(Zend_Controller_Response_Http $response)
	{
		$cacheInstance = Df_PageCache_Model_Cache::getCacheInstance();

		/** @var Df_PageCache_Model_Processor */
		$processor = Mage::getSingleton('df_pagecache/processor');
		$countLimit = Mage::getStoreConfig(Mage_Reports_Block_Product_Viewed::XML_PATH_RECENTLY_VIEWED_COUNT);
		// save recently viewed product count limit
		$cacheId = $processor->getRecentlyViewedCountCacheId();
		if (!$cacheInstance->getFrontend()->test($cacheId)) {
			$cacheInstance->save($countLimit, $cacheId, array(Df_PageCache_Model_Processor::CACHE_TAG));
		}
		// save current product id
		$product = Mage::registry('current_product');
		if ($product) {
			$cacheId = $processor->getRequestCacheId() . '_current_product_id';
			$cacheInstance->save($product->getId(), $cacheId, array(Df_PageCache_Model_Processor::CACHE_TAG));
			$processor->setMetadata(self::METADATA_PRODUCT_ID, $product->getId());
			Df_PageCache_Model_Cookie::registerViewedProducts($product->getId(), $countLimit);
		}

		return parent::prepareContent($response);
	}

	/**
	 * Return cache page id without application. Depends on GET super global array.
	 *
	 * @param Df_PageCache_Model_Processor $processor
	 * @return string
	 */
	public function getPageIdWithoutApp(Df_PageCache_Model_Processor $processor)
	{
		$pageId = parent::getPageIdWithoutApp($processor);
		return $this->_appendCustomerRatesToPageId($pageId);
	}
}
