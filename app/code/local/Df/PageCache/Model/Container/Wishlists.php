<?php
class Df_PageCache_Model_Container_Wishlists extends Df_PageCache_Model_Container_Abstract
{
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
		return 'CONTAINER_WISHLISTS_' . md5($this->_getIdentifier());
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
	 * Render block content from placeholder
	 *
	 * @return string
	 */
	protected function _renderBlock()
	{
		$block = $this->_getPlaceHolderBlock();
		return $block->toHtml();
	}
}
