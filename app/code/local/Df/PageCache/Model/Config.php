<?php
class Df_PageCache_Model_Config extends Varien_Simplexml_Config
{
	protected $_placeholders = null;

	/**
	 * Class constructor
	 * load cache configuration
	 *
	 * @param $data
	 */
	public function __construct($data = null)
	{
		parent::__construct($data);
		$this->setCacheId('cache_config');
		$this->_cacheChecksum   = null;
		$this->_cache = Mage::app()->getCache();

		$canUsaCache = Mage::app()->useCache('config');
		if ($canUsaCache) {
			if ($this->loadCache()) {
				return $this;
			}
		}

		$config = Mage::getConfig()->loadModulesConfiguration('cache.xml');
		$this->setXml($config->getNode());

		if ($canUsaCache) {
			$this->saveCache(array(Mage_Core_Model_Config::CACHE_TAG));
		}
		return $this;
	}

	/**
	 * Initialize all declared placeholders as array
	 * @return Df_PageCache_Model_Config
	 */
	protected function _initPlaceholders()
	{
		if ($this->_placeholders === null) {
			$this->_placeholders = [];
			foreach ($this->getNode('placeholders')->children() as $placeholder) {
				$this->_placeholders[(string)$placeholder->block][] = array(
					'container'     => (string)$placeholder->container,
					'code'          => (string)$placeholder->placeholder,
					'cache_lifetime'=> (int) $placeholder->cache_lifetime,
					'name'          => (string) $placeholder->name
				);
			}
		}
		return $this;
	}

	/**
	 * Retrieve all declared placeholders as array
	 *
	 * @return array
	 */
	public function getDeclaredPlaceholders()
	{
		$this->_initPlaceholders();
		return $this->_placeholders;
	}

	/**
	 * Create placeholder object based on block information
	 *
	 * @param Mage_Core_Block_Abstract $block
	 * @return Df_PageCache_Model_Container_Placeholder
	 */
	public function getBlockPlaceholder($block)
	{
		$this->_initPlaceholders();
		$type = $block->getType();
		if (is_object($type)) {
			//Mage::log(get_class($block));
			//Mage::log(get_class($type));
		}
		else if (isset($this->_placeholders[$type])) {
			$placeholderData = false;
			foreach ($this->_placeholders[$type] as $placeholderInfo) {
				if (!empty($placeholderInfo['name'])) {
					if ($placeholderInfo['name'] == $block->getNameInLayout()) {
						$placeholderData = $placeholderInfo;
					}
				} else {
					$placeholderData = $placeholderInfo;
				}
			}

			if (!$placeholderData) {
				return false;
			}

			$placeholder = $placeholderData['code']
				. ' container="' . $placeholderData['container'] . '"'
				. ' block="' . get_class($block) . '"';
			$placeholder.= ' cache_id="' . $block->getCacheKey() . '"';

			if (!empty($placeholderData['cache_lifetime'])) {
				$placeholder .= ' cache_lifetime="' . $placeholderData['cache_lifetime'] . '"';
			}

			foreach ($block->getCacheKeyInfo() as $k => $v) {
				if (is_string($k) && !empty($k)) {
					$placeholder .= ' ' . $k . '="' . $v . '"';
				}
			}
			$placeholder = Mage::getModel('df_pagecache/container_placeholder', $placeholder);
			return $placeholder;
		}
		return false;
	}
}
