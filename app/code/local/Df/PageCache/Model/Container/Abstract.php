<?php
abstract class Df_PageCache_Model_Container_Abstract
{
	/**
	 * @var null|Df_PageCache_Model_Processor
	 */
	protected $_processor;

	/**
	 * Placeholder instance
	 *
	 * @var Df_PageCache_Model_Container_Placeholder
	 */
	protected $_placeholder;

	/** @var Mage_Core_Block_Template */
	protected $_placeholderBlock;

	/**
	 * @var array
	 */
	protected $_layouts = [];

	/**
	 * Class constructor
	 *
	 * @param Df_PageCache_Model_Container_Placeholder $placeholder
	 */
	public function __construct($placeholder)
	{
		$this->_placeholder = $placeholder;
	}

	/**
	 * Get container individual cache id
	 *
	 * @return string|false
	 */
	protected function _getCacheId()
	{
		return false;
	}

	/**
	 * Generate placeholder content before application was initialized and apply to page content if possible
	 *
	 * @param string $content
	 * @return bool
	 */
	public function applyWithoutApp(&$content)
	{
		$cacheId = $this->_getCacheId();

		if ($cacheId === false) {
			$this->_applyToContent($content, '');
			return true;
		}

		$block = $this->_loadCache($cacheId);
		if ($block === false) {
			return false;
		}

		$block = Df_PageCache_Helper_Url::replaceUenc($block);
		$this->_applyToContent($content, $block);
		return true;
	}

	/**
	 * Generate and apply container content in controller after application is initialized
	 *
	 * @param string $content
	 * @return bool
	 */
	public function applyInApp(&$content)
	{
		$blockContent = $this->_renderBlock();
		if ($blockContent === false) {
			return false;
		}

		if (df_cfg(Df_PageCache_Model_Processor::XML_PATH_CACHE_DEBUG)) {
			$debugBlock = new Df_PageCache_Block_Debug;
			$debugBlock->setDynamicBlockContent($blockContent);
			$debugBlock->setTags($this->_getPlaceHolderBlock()->getCacheTags());

			$debugBlock->setType($this->_placeholder->getName());
			$this->_applyToContent($content, $debugBlock->toHtml());
		} else {
			$this->_applyToContent($content, $blockContent);
		}

		$subprocessor = $this->_processor->getSubprocessor();
		if ($subprocessor) {
			$contentWithoutNestedBlocks = $subprocessor->replaceContentToPlaceholderReplacer($blockContent);
			$this->saveCache($contentWithoutNestedBlocks);
		}

		return true;
	}

	/**
	 * Save rendered block content to cache storage
	 *
	 * @param string $blockContent
	 * @param array $tags
	 * @return Df_PageCache_Model_Container_Abstract
	 */
	public function saveCache($blockContent, $tags = array())
	{
		$cacheId = $this->_getCacheId();
		if ($cacheId !== false) {
			$this->_saveCache($blockContent, $cacheId, $tags);
		}
		return $this;
	}

	/**
	 * Render block content from placeholder
	 *
	 * @return string|false
	 */
	protected function _renderBlock()
	{
		return false;
	}

	/**
	 * Replace container placeholder in content on container content
	 *
	 * @param string $content
	 * @param string $containerContent
	 */
	protected function _applyToContent(&$content, $containerContent)
	{
		$containerContent = $this->_placeholder->getStartTag() . $containerContent . $this->_placeholder->getEndTag();
		$content = str_replace($this->_placeholder->getReplacer(), $containerContent, $content);
	}

	/**
	 * Load cached data by cache id
	 *
	 * @param string $id
	 * @return string|false
	 */
	protected function _loadCache($id)
	{
		return Df_PageCache_Model_Cache::getCacheInstance()->load($id);
	}

	/**
	 * Save data to cache storage
	 *
	 * @param string $data
	 * @param string $id
	 * @param array $tags
	 * @param null|int $lifetime
	 * @return Df_PageCache_Model_Container_Abstract
	 */
	protected function _saveCache($data, $id, $tags = array(), $lifetime = null)
	{
		$tags[] = Df_PageCache_Model_Processor::CACHE_TAG;
		$tags = array_merge($tags, $this->_getPlaceHolderBlock()->getCacheTags());
		if (is_null($lifetime)) {
			$lifetime = $this->_placeholder->getAttribute('cache_lifetime') !== null ?
				$this->_placeholder->getAttribute('cache_lifetime') : false;
		}
		Df_PageCache_Helper_Data::prepareContentPlaceholders($data);
		Df_PageCache_Model_Cache::getCacheInstance()->save($data, $id, $tags, $lifetime);
		return $this;
	}

	/**
	 * Retrieve cookie value
	 *
	 * @param string $cookieName
	 * @param mixed $defaultValue
	 * @return string
	 */
	protected function _getCookieValue($cookieName, $defaultValue = null)
	{
		return (array_key_exists($cookieName, $_COOKIE) ? $_COOKIE[$cookieName] : $defaultValue);
	}

	/**
	 * Set processor for container needs
	 *
	 * @param Df_PageCache_Model_Processor $processor
	 * @return Df_PageCache_Model_Container_Abstract
	 */
	public function setProcessor(Df_PageCache_Model_Processor $processor)
	{
		$this->_processor = $processor;
		return $this;
	}

	/**
	 * Get last visited category id
	 *
	 * @return string|null
	 */
	protected function _getCategoryId()
	{
		if ($this->_processor) {
			$categoryId = $this->_processor
				->getMetadata(Df_PageCache_Model_Processor_Category::METADATA_CATEGORY_ID);
			if ($categoryId) {
				return $categoryId;
			}
		}

		//If it is not product page and not category page - we have no any category (not using last visited)
		if (!$this->_getProductId()) {
			return null;
		}

		return $this->_getCookieValue(Df_PageCache_Model_Cookie::COOKIE_CATEGORY_ID, null);
	}

	/**
	 * Get current product id
	 *
	 * @return string|null
	 */
	protected function _getProductId()
	{
		if (!$this->_processor) {
			return null;
		}

		return $this->_processor->getMetadata(Df_PageCache_Model_Processor_Product::METADATA_PRODUCT_ID);
	}

	/**
	 * Get current request id
	 *
	 * @return string|null
	 */
	protected function _getRequestId()
	{
		return !$this->_processor ? null : $this->_processor->getRequestId();
	}

	/**
	 * Get Placeholder Block
	 *
	 * @return Mage_Core_Block_Abstract
	 */
	protected function _getPlaceHolderBlock()
	{
		if (null === $this->_placeholderBlock) {
			$blockName = $this->_placeholder->getAttribute('block');
			$this->_placeholderBlock = new $blockName;
			$this->_placeholderBlock->setTemplate($this->_placeholder->getAttribute('template'));
			$this->_placeholderBlock->setLayout(Mage::app()->getLayout());
			/** @noinspection PhpUndefinedMethodInspection */
			$this->_placeholderBlock->setSkipRenderTag(true);
		}
		return $this->_placeholderBlock;
	}

	/**
	 * Set placeholder block
	 *
	 * @param Mage_Core_Block_Abstract $block
	 * @return Df_PageCache_Model_Container_Abstract
	 */
	public function setPlaceholderBlock(Mage_Core_Block_Abstract $block) {
		$this->_placeholderBlock = $block;
		return $this;
	}

	/**
	 * Get layout with generated blocks
	 *
	 * @param string $handler
	 * @return Mage_Core_Model_Layout
	 */
	protected function _getLayout($handler = 'default')
	{
		if (!isset($this->_layouts[$handler])) {
			$layout = Mage::app()->getLayout();
			$layout->getUpdate()->load($handler);
			$layout->generateXml();
			$layout->generateBlocks();
			$this->_layouts[$handler] = $layout;
		}
		return $this->_layouts[$handler];
	}
}
