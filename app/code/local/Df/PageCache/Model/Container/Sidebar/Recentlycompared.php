<?php
class Df_PageCache_Model_Container_Sidebar_Recentlycompared
	extends Df_PageCache_Model_Container_Abstract
{
	/**
	 * Get identifier from cookies
	 *
	 * @return string
	 */
	protected function _getIdentifier()
	{
		return $this->_getCookieValue(Df_PageCache_Model_Cookie::COOKIE_RECENTLY_COMPARED, '')
			. $this->_getCookieValue(Df_PageCache_Model_Cookie::COOKIE_CUSTOMER, '');
	}

	/**
	 * Get cache identifier
	 *
	 * @return string
	 */
	protected function _getCacheId()
	{
		return 'CONTAINER_RECENTLYCOMPARED_' . md5($this->_placeholder->getAttribute('cache_id')
				. $this->_getIdentifier());
	}

	/**
	 * Render block content
	 *
	 * @return string
	 */
	protected function _renderBlock()
	{
		return $this->_getPlaceHolderBlock()->toHtml();
	}
}
