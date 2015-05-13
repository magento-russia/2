<?php
class Df_PageCache_Model_Container_Orders extends Df_PageCache_Model_Container_Advanced_Abstract
{
	const CACHE_TAG_PREFIX = 'orders';

	/**
	 * Get identifier from cookies
	 *
	 * @return string
	 */
	protected function _getIdentifier()
	{
		return $this->_getCookieValue(Df_PageCache_Model_Cookie::COOKIE_CUSTOMER, '');
	}

	/**
	 * Get cache identifier
	 *
	 * @return string
	 */
	protected function _getCacheId()
	{
		return md5($this->_getIdentifier());
	}

	/**
	 * Retrieve cache identifier
	 *
	 * @return string
	 */
	public function getCacheId()
	{
		return $this->_getCacheId();
	}

	/**
	 * Get container individual additional cache id
	 *
	 * @return string | false
	 */
	protected function _getAdditionalCacheId()
	{
		return md5('CONTAINER_ORDERS_' . $this->_placeholder->getAttribute('cache_id'));
	}

	/**
	 * Render block content
	 *
	 * @return string
	 */
	protected function _renderBlock()
	{
		$block = $this->_getPlaceHolderBlock();
		Mage::dispatchEvent('render_block', array('block' => $block, 'placeholder' => $this->_placeholder));
		return $block->toHtml();
	}
}
