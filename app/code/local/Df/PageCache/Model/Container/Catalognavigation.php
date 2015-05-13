<?php
class Df_PageCache_Model_Container_Catalognavigation extends Df_PageCache_Model_Container_Abstract
{
	/**
	 * @return string
	 */
	protected function _getBlockCacheId()
	{
		return $this->_placeholder->getAttribute('short_cache_id');
	}

	/**
	 * @return string
	 */
	protected function _getCategoryCacheId()
	{
		$shortCacheId = $this->_placeholder->getAttribute('short_cache_id');
		$categoryPath = $this->_placeholder->getAttribute('entity_key');
		$categoryId = $this->_getCategoryId();
		if (!$shortCacheId || !$categoryPath) {
			return false;
		}
		return $shortCacheId . '_' . $categoryPath . ($categoryId ? ('_' . $categoryId) : '');
	}

	/**
	 * Generate placeholder content before application was initialized and apply to page content if possible
	 *
	 * @param string $content
	 * @return bool
	 */
	public function applyWithoutApp(&$content)
	{
		$blockCacheId = $this->_getBlockCacheId();
		$categoryCacheId = $this->_getCategoryCacheId();
		if ($blockCacheId && $categoryCacheId) {
			$blockContent = $this->_loadCache($blockCacheId);
			$categoryUniqueClasses = $this->_loadCache($categoryCacheId);
			if ($blockContent !== false && $categoryUniqueClasses !== false) {
				if ($categoryUniqueClasses != '') {
					$regexp = '';
					foreach (explode(' ', $categoryUniqueClasses) as $categoryUniqueClass) {
						$regexp .= ($regexp ? '|' : '') . preg_quote($categoryUniqueClass);
					}
					$blockContent = preg_replace('/(?<=\s|")(' . $regexp . ')(?=\s|")/u', '$1 active', $blockContent);
				}
				$this->_applyToContent($content, $blockContent);
				return true;
			}
		}
		return false;
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
		$blockCacheId = $this->_getBlockCacheId();
		if ($blockCacheId) {
			$categoryCacheId = $this->_getCategoryCacheId();
			if ($categoryCacheId) {
				$categoryUniqueClasses = '';
				$classes = array();
				$classesCount = preg_match_all('/< *li[^>]*class *= *["\']?([^"\']*)/i', $blockContent, $classes);
				for ($i = 0; $i < $classesCount; $i++) {
					$classAttribute = $classes[0][$i];
					$classValue = $classes[1][$i];
					if (false === strpos($classAttribute, 'active')) {
						continue;
					}
					$classInactive = preg_replace('/\s+active|active\s+|active/', '', $classAttribute);
					$blockContent = str_replace($classAttribute, $classInactive, $blockContent);
					$matches = array();
					if (preg_match('/(?<=\s|^)nav-.+?(?=\s|$)/', $classValue, $matches)) {
						$categoryUniqueClasses .= ($categoryUniqueClasses ? ' ' : '') . $matches[0];
					}
				}
				$this->_saveCache($categoryUniqueClasses, $categoryCacheId);
			}
			if (!Df_PageCache_Model_Cache::getCacheInstance()->getFrontend()->test($blockCacheId)) {
				$this->_saveCache($blockContent, $blockCacheId, $tags);
			}
		}
		return $this;
	}

	/**
	 * Render block content
	 *
	 * @return string
	 */
	protected function _renderBlock()
	{
		$layout = $this->_getLayout('default');
		$block = $layout->getBlock('catalog.topnav');
		$block->setSkipRenderTag(true);

		$categoryId = $this->_getCategoryId();
		if (!Mage::registry('current_category') && $categoryId) {
			$category = Mage::getModel('catalog/category')->load($categoryId);
			Mage::register('current_category', $category);
			Mage::register('current_entity_key', $category->getPath());
		}

		Mage::dispatchEvent('render_block', array('block' => $block, 'placeholder' => $this->_placeholder));

		return $block->toHtml();
	}
}
