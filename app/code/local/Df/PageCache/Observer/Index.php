<?php
class Df_PageCache_Observer_Index
{
	/**
	 * Clean cache by specified entity and its ids
	 *
	 * @param Mage_Core_Model_Abstract $entity
	 * @param array $ids
	 */
	protected function _cleanEntityCache(Mage_Core_Model_Abstract $entity, array $ids)
	{
		$cacheTags = [];
		foreach ($ids as $entityId) {
			$entity->setId($entityId);
			$cacheTags = array_merge($cacheTags, $entity->getCacheIdTags());
		}
		if (!empty($cacheTags)) {
			Df_PageCache_Model_Cache::getCacheInstance()->clean($cacheTags);
		}
	}

	/**
	 * Invalidate FPC after full reindex
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function invalidateCacheAfterFullReindex(Varien_Event_Observer $observer)
	{
		Mage::app()->getCacheInstance()->invalidateType('full_page');
	}

	/**
	 * Clean cache for affected products
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function cleanProductsCacheAfterPartialReindex(Varien_Event_Observer $observer)
	{
		$entityIds = $observer->getEvent()->getProductIds();
		if (is_array($entityIds)) {
			$this->_cleanEntityCache(Mage::getModel('catalog/product'), $entityIds);
		}
	}

	/**
	 * Clean cache for affected categories
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function cleanCategoriesCacheAfterPartialReindex(Varien_Event_Observer $observer)
	{
		$entityIds = $observer->getEvent()->getCategoryIds();
		if (is_array($entityIds)) {
			$this->_cleanEntityCache(Mage::getModel('catalog/category'), $entityIds);
		}
	}

	/**
	 * Cleans cache by tags
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function cleanCacheByTags(Varien_Event_Observer $observer)
	{
		$tags = $observer->getEvent()->getTags();

		if (!empty($tags)) {
			Df_PageCache_Model_Cache::getCacheInstance()->clean($tags);
		}
	}

	/**
	 * Clear request path cache by tag
	 * (used for redirects invalidation)
	 *
	 * @param Varien_Event_Observer $observer
	 * @return $this
	 */
	public function clearRequestCacheByTag(Varien_Event_Observer $observer)
	{
		$redirects = $observer->getEvent()->getRedirects();
		foreach ($redirects as $redirect) {
			Df_PageCache_Model_Cache::getCacheInstance()->clean(
				array(
					Df_PageCache_Helper_Url::prepareRequestPathTag($redirect['identifier']),
				)
			);
		}
		return $this;
	}
}
