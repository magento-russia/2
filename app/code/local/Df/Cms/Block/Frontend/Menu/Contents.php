<?php
class Df_Cms_Block_Frontend_Menu_Contents extends Mage_Core_Block_Abstract {
	/**
	 * @override
	 * @return string[]
	 */
	public function getCacheKeyInfo() {
		return array(
			rm_state()->getController()->getFullActionName()
			, $this->getMenu()->getPosition()
			, Mage::app()->getStore()->getCode()
		);
	}

	/**
	 * Чтобы блок кэшировался стандартным, заложенным в @see Mage_Core_Block_Abstract способом,
	 * продолжительность хранения кэша надо указывать обязательно,
	 * потому что значением продолжительности по умолчанию является «null»,
	 * что в контексте @see Mage_Core_Block_Abstract
	 * (и в полную противоположность Zend Framework
	 * и всем остальным частям Magento, где используется кэширование)
	 * означает, что блок не удет кэшироваться вовсе!
	 * @see Mage_Core_Block_Abstract::_loadCache()
	 * @override
	 * @return int|bool|null
	 */
	public function getCacheLifetime() {return Df_Core_Block_Template::CACHE_LIFETIME_STANDARD;}

	/** @return Df_Cms_Model_ContentsMenu */
	public function getMenu() {return $this->_getData(self::P__MENU);}

	/**
	 * @override
	 * @return string
	 */
	protected function _toHtml() {
		/** @var string $result */
		$result = '';
		if (
				df_cfg()->cms()->hierarchy()->isEnabled()
			&&
				df_enabled(Df_Core_Feature::CMS_2)
			&&
				count($this->getMenu()->getApplicators())
		) {
			/** @var array $renderedNodes */
			$renderedNodes = array();
			foreach (df_h()->cms()->getTree()->getTree()->getNodes() as $node) {
				/** @var Df_Cms_Varien_Data_Tree_Node $node */
				/** @var Df_Cms_Model_Hierarchy_Node $cmsNode */
				$cmsNode = $node->getCmsNode();
				if (
						!$cmsNode->getParentNodeId()
					&&
						in_array($cmsNode->getId(), $this->getMenu()->getRootNodeIds())
				) {
					$renderedNodes[]=
						$this->createHtmlListItem(
							$this->renderNode(
								$node
								,(0 === $cmsNode->getMenuLevelsDown())
								? null
								: $cmsNode->getMenuLevelsDown()
							)
							,df_clean(array(
								'class' => count($node->getChildren()) ? 'parent' : null
							))
						)					;
				}
			}
			if (count($renderedNodes)) {
				$result =
					rm_tag(
						'div'
						,array('class' => 'df-cms-menu-wrapper')
						,$this->createHtmlList(implode($renderedNodes), array('class' => 'cms-menu'))
					)
				;
			}
		}
		return $result;
	}

	/**
	 * @param string $content
	 * @param array $attributes[optional]
	 * @return string
	 */
	private function createHtmlList($content, array $attributes = array()) {
		return rm_tag('ul', $attributes, $content);
	}

	/**
	 * @param string $content
	 * @param array $attributes[optional]
	 * @return string
	 */
	private function createHtmlListItem($content, array $attributes = array()) {
		return rm_tag('li', $attributes, $content);
	}

	/**
	 * @param Df_Cms_Varien_Data_Tree_Node $parent
	 * @param int|null $menuLevelsDown[optional]
	 * @return string
	 */
	private function renderChildren(Df_Cms_Varien_Data_Tree_Node $parent, $menuLevelsDown = null) {
		/** @var array $renderedNodes */
		$renderedNodes = array();
		if (is_null($menuLevelsDown) || (0 < $menuLevelsDown)) {
			foreach ($parent->getChildren() as $childNode) {
				/** @var Df_Cms_Varien_Data_Tree_Node $childNode */
				$renderedNodes[]=
					$this->createHtmlListItem(
						$this->renderNode($childNode, $menuLevelsDown)
						,df_clean(array(
							'class' => $childNode->getChildren() ? 'parent' : null
						))
					)
				;
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
			? rm_tag('span', rm_tag('strong', array(), $cmsNode->getLabel()))
			: rm_tag('a', array('href' => $cmsNode->getUrl()), $cmsNode->getLabel())
		;
	}

	/**
	 * @param Df_Cms_Varien_Data_Tree_Node $node
	 * @param int|null $menuLevelsDown[optional]
	 * @return string
	 */
	private function renderNode(Df_Cms_Varien_Data_Tree_Node $node, $menuLevelsDown = null) {
		return
			df_concat(
				$this->renderLabel($node)
				,$this->renderChildren($node, is_null($menuLevelsDown) ? null : $menuLevelsDown - 1)
			)
		;
	}

	const _CLASS = __CLASS__;
	const P__MENU = 'menu';
	/**
	 * @param Df_Cms_Model_ContentsMenu $menu
	 * @return Df_Cms_Block_Frontend_Menu_Contents
	 */
	public static function i(Df_Cms_Model_ContentsMenu $menu) {
		return df_block(__CLASS__, null, array(self::P__MENU => $menu));
	}
}