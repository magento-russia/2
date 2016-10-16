<?php
class Df_PageCache_Model_Container_Breadcrumbs extends Df_PageCache_Model_Container_Abstract
{
	/**
	 * Get cache identifier
	 *
	 * @return string
	 */
	protected function _getCacheId()
	{
		if ($this->_getCategoryId() || $this->_getProductId()) {
			$cacheSubKey = '_' . $this->_getCategoryId()
				. '_' . $this->_getProductId();
		} else {
			$cacheSubKey = $this->_getRequestId();
		}

		return 'CONTAINER_BREADCRUMBS_'
			. md5($this->_placeholder->getAttribute('cache_id') . $cacheSubKey);
	}

	/**
	 * Render block content
	 *
	 * @return string
	 */
	protected function _renderBlock()
	{
		$productId = $this->_getProductId();

		/** @var $product null|Mage_Catalog_Model_Product */
		$product = null;

		if ($productId) {
			/** @var Df_Catalog_Model_Product $product */
			$product = Mage::getModel('catalog/product');
			$product->setStoreId(Mage::app()->getStore()->getId());
			$product->load($productId);
			if ($product) {
				Mage::register('current_product', $product);
			}
		}
		$categoryId = $this->_getCategoryId();

		if ($product !== null && !$product->canBeShowInCategory($categoryId)) {
			$categoryId = null;
			Mage::unregister('current_category');
		}

		if ($categoryId && !Mage::registry('current_category')) {
			$category = Mage::getModel('catalog/category')->load($categoryId);
			if ($category) {
				Mage::register('current_category', $category);
			}
		}

		//No need breadcrumbs on CMS pages
		if (!$productId && !$categoryId) {
			return '';
		}

		/** @var $breadcrumbsBlock Mage_Page_Block_Html_Breadcrumbs */
		$breadcrumbsBlock = $this->_getPlaceHolderBlock();
		$breadcrumbsBlock->setNameInLayout('breadcrumbs');
		$crumbs = $this->_placeholder->getAttribute('crumbs');
		if ($crumbs) {
			$crumbs = unserialize(base64_decode($crumbs));
			foreach ($crumbs as $crumbName => $crumbInfo) {
				$breadcrumbsBlock->addCrumb($crumbName, $crumbInfo);
			}
		}
		Mage::dispatchEvent('render_block', array('block' => $breadcrumbsBlock, 'placeholder' => $this->_placeholder));
		return $breadcrumbsBlock->toHtml();
	}
}
