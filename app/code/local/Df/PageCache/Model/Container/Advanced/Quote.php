<?php
abstract class Df_PageCache_Model_Container_Advanced_Quote
	extends Df_PageCache_Model_Container_Advanced_Abstract
{
	/**
	 * Cache tag prefix
	 */
	const CACHE_TAG_PREFIX = 'quote_';

	/**
	 * Get cache identifier
	 *
	 * @return string
	 */
	public static function getCacheId()
	{
		$cookieCart = Df_PageCache_Model_Cookie::COOKIE_CART;
		$cookieCustomer = Df_PageCache_Model_Cookie::COOKIE_CUSTOMER;
		return md5(Df_PageCache_Model_Container_Advanced_Quote::CACHE_TAG_PREFIX
			. (array_key_exists($cookieCart, $_COOKIE) ? $_COOKIE[$cookieCart] : '')
			. (array_key_exists($cookieCustomer, $_COOKIE) ? $_COOKIE[$cookieCustomer] : ''));
	}

	/**
	 * Get cache identifier
	 *
	 * @return string
	 */
	protected function _getCacheId()
	{
		return Df_PageCache_Model_Container_Advanced_Quote::getCacheId();
	}

	/**
	 * Get container individual additional cache id
	 *
	 * @return string
	 */
	protected function _getAdditionalCacheId()
	{
		return md5($this->_placeholder->getName() . '_' . $this->_placeholder->getAttribute('cache_id'));
	}
}
