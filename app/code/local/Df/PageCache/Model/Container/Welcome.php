<?php
class Df_PageCache_Model_Container_Welcome extends Df_PageCache_Model_Container_Customer
{
	/**
	 * Get identifier from cookies
	 *
	 * @return string
	 */
	protected function _getIdentifier()
	{
		$cacheId = $this->_getCookieValue(Df_PageCache_Model_Cookie::COOKIE_CUSTOMER, '')
			. '_'
			. $this->_getCookieValue(Df_PageCache_Model_Cookie::COOKIE_CUSTOMER_LOGGED_IN, '')
			. '_'
			. $this->_getCookieValue(Df_PageCache_Model_Cookie::PERSISTENT_COOKIE_NAME, '');
		return $cacheId;
	}

	/**
	 * Get cache identifier
	 *
	 * @return string
	 */
	protected function _getCacheId()
	{
		return 'CONTAINER_WELCOME_' . md5($this->_placeholder->getAttribute('cache_id') . $this->_getIdentifier());
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
