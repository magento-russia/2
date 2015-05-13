<?php
class Df_PageCache_Model_Container_Viewedproducts extends Df_PageCache_Model_Container_Abstract
{
	const COOKIE_NAME = 'VIEWED_PRODUCT_IDS';

	/**
	 * Get viewed product ids from cookie
	 *
	 * @return array
	 */
	protected function _getProductIds()
	{
		$result = $this->_getCookieValue(self::COOKIE_NAME, array());
		if ($result) {
			$result = explode(',', $result);
		}
		return $result;
	}

	/**
	 * Get cache identifier
	 *
	 * @return string
	 */
	protected function _getCacheId()
	{
		$cacheId = $this->_placeholder->getAttribute('cache_id');
		$productIds = $this->_getProductIds();
		if ($cacheId && $productIds) {
			$cacheId = 'CONTAINER_' . md5($cacheId . implode('_', $productIds)
				. $this->_getCookieValue(Mage_Core_Model_Store::COOKIE_CURRENCY, ''));
			return $cacheId;
		}
		return false;
	}

	/**
	 * Render block content
	 *
	 * @return string
	 */
	protected function _renderBlock()
	{
		/** @var $block Mage_Reports_Block_Product_Abstract */
		$block = $this->_getPlaceHolderBlock();
		$block->setProductIds($this->_getProductIds());
		$block->useProductIdsOrder();
		Mage::dispatchEvent('render_block', array('block' => $block, 'placeholder' => $this->_placeholder));
		return $block->toHtml();
	}
}
