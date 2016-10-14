<?php
class Df_PageCache_Model_Processor_Default
{
	/**
	 * @var Df_PageCache_Model_Container_Placeholder
	 */
	private $_placeholder;

	/**
	 * Disable cache for url with next GET params
	 *
	 * @var array
	 */
	protected $_noCacheGetParams = array('___store', '___from_store');

	/**
	 * Is cache allowed
	 *
	 * @var null|bool
	 */
	protected $_allowCache = null;

	/**
	 * Check if request can be cached
	 *
	 * @param Zend_Controller_Request_Http $request
	 * @return bool
	 */
	public function allowCache(Zend_Controller_Request_Http $request)
	{
		if (is_null($this->_allowCache)) {
			foreach ($this->_noCacheGetParams as $param) {
				if (!is_null($request->getParam($param, null))) {
					$this->_allowCache = false;
					return $this->_allowCache;
				}
			}
			if (Mage::getSingleton('core/session')->getNoCacheFlag()) {
				$this->_allowCache = false;
				return $this->_allowCache;
			}
			$this->_allowCache = true;
			return $this->_allowCache;
		}
		return $this->_allowCache;
	}


	/**
	 * Replace block content to placeholder replacer
	 *
	 * @param string $content
	 * @return string
	 */
	public function replaceContentToPlaceholderReplacer($content)
	{
		$placeholders = array();
		preg_match_all(
			Df_PageCache_Model_Container_Placeholder::HTML_NAME_PATTERN,
			$content,
			$placeholders,
			PREG_PATTERN_ORDER
		);
		$placeholders = array_unique($placeholders[1]);
		try {
			foreach ($placeholders as $definition) {
				$this->_placeholder = Mage::getModel('df_pagecache/container_placeholder', $definition);
				$content = preg_replace_callback($this->_placeholder->getPattern(),
					array($this, '_getPlaceholderReplacer'), $content);
			}
			$this->_placeholder = null;
		} catch (Exception $e) {
			$this->_placeholder = null;
			throw $e;
		}
		return $content;
	}

	/**
	 * Prepare response body before caching
	 *
	 * @param Zend_Controller_Response_Http $response
	 * @return string
	 */
	public function prepareContent(Zend_Controller_Response_Http $response)
	{
		return $this->replaceContentToPlaceholderReplacer($response->getBody());
	}

	/**
	 * Retrieve placeholder replacer
	 *
	 * @param array $matches Matches by preg_replace_callback
	 * @return string
	 */
	protected function _getPlaceholderReplacer($matches)
	{
		return $this->_placeholder->getReplacer();
	}


	/**
	 * Return cache page id with application. Depends on GET super global array.
	 *
	 * @param Df_PageCache_Model_Processor $processor
	 * @param Zend_Controller_Request_Http $request
	 * @return string
	 */
	public function getPageIdInApp(Df_PageCache_Model_Processor $processor)
	{
		return $this->getPageIdWithoutApp($processor);
	}

	/**
	 * Return cache page id without application. Depends on GET super global array.
	 *
	 * @param Df_PageCache_Model_Processor $processor
	 * @return string
	 */
	public function getPageIdWithoutApp(Df_PageCache_Model_Processor $processor)
	{
		$queryParams = $_GET;
		ksort($queryParams);
		$queryParamsHash = md5(serialize($queryParams));
		return $processor->getRequestId() . '_' . $queryParamsHash;
	}

	/**
	 * Append customer rates cookie to page id
	 *
	 * @param string $pageId
	 * @return string
	 */
	protected function _appendCustomerRatesToPageId($pageId)
	{
		if (isset($_COOKIE[Df_PageCache_Model_Cookie::COOKIE_CUSTOMER_RATES])) {
			$pageId .= '_' . $_COOKIE[Df_PageCache_Model_Cookie::COOKIE_CUSTOMER_RATES];
		}
		return $pageId;
	}
}
