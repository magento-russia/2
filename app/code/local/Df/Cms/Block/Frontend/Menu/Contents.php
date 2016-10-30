<?php
class Df_Cms_Block_Frontend_Menu_Contents extends Df_Core_Block_Abstract {
	/**
	 * @override
	 * @see Df_Core_Block_Abstract::cacheKeySuffix()
	 * @used-by Df_Core_Block_Abstract::getCacheKeyInfo()
	 * @return string|string[]
	 */
	public function cacheKeySuffix() {return $this->getMenu()->getPosition();}

	/**
	 * Чтобы блок кэшировался стандартным, заложенным в @see Mage_Core_Block_Abstract способом,
	 * продолжительность хранения кэша надо указывать обязательно,
	 * потому что значением продолжительности по умолчанию является «null»,
	 * что в контексте @see Mage_Core_Block_Abstract
	 * (и в полную противоположность Zend Framework
	 * и всем остальным частям Magento, где используется кэширование)
	 * означает, что блок не удет кэшироваться вовсе!
	 * @used-by Mage_Core_Block_Abstract::_loadCache()
	 * @override
	 * @return int|bool|null
	 */
	public function getCacheLifetime() {return Df_Core_Block_Template::CACHE_LIFETIME_STANDARD;}

	/** @return Df_Cms_Model_ContentsMenu */
	public function getMenu() {return $this[self::$P__MENU];}

	/**
	 * @override
	 * @see Mage_Core_Block_Abstract::_toHtml()
	 * @used-by Mage_Core_Block_Abstract::toHtml()
	 * @return string
	 */
	protected function _toHtml() {
		return df_tag(
			'div'
			, array('class' => 'df-cms-menu-wrapper')
			, $this->createHtmlList(implode($this->getRenderedNodes()), array('class' => 'cms-menu'))
		);
	}

	/**
	 * @override
	 * @see Df_Core_Block_Abstract::needToShow()
	 * @used-by Df_Core_Block_Abstract::_loadCache()
	 * @used-by Df_Core_Block_Abstract::getCacheKey()
	 * @return bool
	 */
	protected function needToShow() {
		return
			df_cfgr()->cms()->hierarchy()->isEnabled()
			&& $this->getMenu()->getApplicators()->hasItems()
			&& $this->getRenderedNodes()
		;
	}

	/**
	 * @override
	 * @see Df_Core_Block_Abstract::shouldCachePerRequestAction()
	 * @used-by Df_Core_Block_Abstract::getCacheKeyInfo()
	 * @return bool
	 */
	protected function shouldCachePerRequestAction() {return true;}

	/**
	 * @param string $content
	 * @param array $attributes [optional]
	 * @return string
	 */
	private function createHtmlList($content, array $attributes = array()) {
		return df_tag('ul', $attributes, $content);
	}

	/**
	 * @param string $content
	 * @param array $attributes [optional]
	 * @return string
	 */
	private function createHtmlListItem($content, array $attributes = array()) {
		return df_tag('li', $attributes, $content);
	}

	/** @return string[] */
	private function getRenderedNodes() {
		if (!isset($this->{__METHOD__})) {
			/** @var string[] $result */
			$result = array();
			foreach (df_h()->cms()->getTree()->getTree()->getNodes() as $node) {
				/** @var Df_Cms_Varien_Data_Tree_Node $node */
				/** @var Df_Cms_Model_Hierarchy_Node $cmsNode */
				$cmsNode = $node->getCmsNode();
				if (
						!$cmsNode->getParentNodeId()
					&&
						in_array($cmsNode->getId(), $this->getMenu()->getRootNodeIds())
				) {
					$result[]= $this->createHtmlListItem(
						$this->renderNode(
							$node
							, !$cmsNode->getMenuLevelsDown() ? null : $cmsNode->getMenuLevelsDown()
						)
						,array_filter(array(
							'class' => $node->getChildren()->count() ? 'parent' : null
						))
					);
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param Df_Cms_Varien_Data_Tree_Node $parent
	 * @param int|null $menuLevelsDown [optional]
	 * @return string
	 */
	private function renderChildren(Df_Cms_Varien_Data_Tree_Node $parent, $menuLevelsDown = null) {
		/** @var array $renderedNodes */
		$renderedNodes = array();
		if (is_null($menuLevelsDown) || (0 < $menuLevelsDown)) {
			foreach ($parent->getChildren() as $childNode) {
				/** @var Df_Cms_Varien_Data_Tree_Node $childNode */
				$renderedNodes[]= $this->createHtmlListItem(
					$this->renderNode($childNode, $menuLevelsDown)
					,array_filter(array('class' => $childNode->getChildren() ? 'parent' : null))
				);
			}
		}
		return !$renderedNodes ? '' : $this->createHtmlList(implode($renderedNodes));
	}

	/**
	 * @param Df_Cms_Varien_Data_Tree_Node $node
	 * @return string
	 */
	private function renderLabel(Df_Cms_Varien_Data_Tree_Node $node) {
		/** @var Df_Cms_Model_Hierarchy_Node $cmsNode */
		$cmsNode = $node->getCmsNode();
		return
					df_h()->cms()->getCurrentNode()
				&&
					($cmsNode->getId() === df_h()->cms()->getCurrentNode()->getId())
			? df_tag('span', array(), df_tag('strong', array(), $cmsNode->getLabel()))
			: df_tag('a', array('href' => $cmsNode->getUrl()), $cmsNode->getLabel())
		;
	}

	/**
	 * @param Df_Cms_Varien_Data_Tree_Node $node
	 * @param int|null $menuLevelsDown [optional]
	 * @return string
	 */
	private function renderNode(Df_Cms_Varien_Data_Tree_Node $node, $menuLevelsDown = null) {
		return
			df_c(
				$this->renderLabel($node)
				,$this->renderChildren($node, is_null($menuLevelsDown) ? null : $menuLevelsDown - 1)
			)
		;
	}

	/** @var string */
	private static $P__MENU = 'menu';

	/**
	 * @used-by Df_Cms_Block_Frontend_Menu_Contents::insertIntoLayout()
	 * @param Df_Cms_Model_ContentsMenu $menu
	 * @return Df_Cms_Block_Frontend_Menu_Contents
	 */
	public static function i(Df_Cms_Model_ContentsMenu $menu) {
		return df_block_l(__CLASS__, array(self::$P__MENU => $menu));
	}
}