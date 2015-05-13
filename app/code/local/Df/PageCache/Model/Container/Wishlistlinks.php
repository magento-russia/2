<?php
class Df_PageCache_Model_Container_Wishlistlinks extends Df_PageCache_Model_Container_Abstract
{
	/**
	 * Get identifier from cookies
	 *
	 * @return string
	 */
	protected function _getIdentifier()
	{
		return $this->_getCookieValue(Df_PageCache_Model_Cookie::COOKIE_WISHLIST_ITEMS, '')
			. $this->_getCookieValue(Df_PageCache_Model_Cookie::COOKIE_CUSTOMER, '');
	}

	/**
	 * Get cache identifier
	 *
	 * @return string
	 */
	protected function _getCacheId()
	{
		return 'CONTAINER_WISHLINKS_' . md5($this->_placeholder->getAttribute('cache_id') . $this->_getIdentifier());
	}

	/**
	 * Render block content
	 *
	 * @return string
	 */
	protected function _renderBlock()
	{
		$block = $this->_placeholder->getAttribute('block');
		$block = new $block;
		$block->setLayout(Mage::app()->getLayout());
		Mage::dispatchEvent('render_block', array('block' => $block, 'placeholder' => $this->_placeholder));
		return $block->toHtml();
	}
}
