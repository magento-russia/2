<?php
class Df_Page_Block_Html_Topmenu extends Mage_Page_Block_Html_Topmenu {
	/**
	 * Цель перекрытия —
	 * кэширование меню.
	 * @override
	 * @param Varien_Data_Tree_Node $menuTree
	 * @param string $childrenWrapClass
	 * @return string
	 */
	protected function _getHtml(Varien_Data_Tree_Node $menuTree, $childrenWrapClass) {
		/** @var string $result */
		$result = null;
		/** @var string $cacheKey */
		/** @var bool $needCacheRm */
		$needCacheRm = (!$menuTree->getParent()) && $this->needCacheRm();
		if ($needCacheRm) {
			/** @var string[] $cacheKeyParams */
			$cacheKeyParams = array($menuTree->getData('outermost_class'), $childrenWrapClass);
			$cacheKey = $this->getCacheRm()->makeKey(__METHOD__, $cacheKeyParams);
			$result = $this->getCacheRm()->loadData($cacheKey);
		}
		if (!$result) {
			/** @noinspection PhpDeprecationInspection */
			$result = parent::_getHtml($menuTree, $childrenWrapClass);
			if ($needCacheRm) {
				$this->getCacheRm()->saveData($cacheKey, $result);
			}
		}
		return $result . $this->getJsRm($menuTree);
	}

	/**
	 * @override
	 * @param Varien_Data_Tree_Node $item
	 * @return string[]
	 */
	protected function _getMenuItemClasses(Varien_Data_Tree_Node $item) {
		/** @var string[] $result */
		$result = [];
		$result[] = 'level' . $item->getData('level');
		$result[] = $item->getData('position_class');
		if ($item->getData('is_first')) {
			$result[] = 'first';
		}
		if ($item->getData('is_last')) {
			$result[] = 'last';
		}
		if ($item->getData('class')) {
			$result[] = $item->getData('class');
		}
		if ($item->hasChildren()) {
			$result[] = 'parent';
		}
		$result[] = 'id-' . $item->getId();
		return $result;
	}

	/** @return Df_Core_Model_Cache */
	private function getCacheRm() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Core_Model_Cache::i(Mage_Core_Block_Abstract::CACHE_GROUP);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param Varien_Data_Tree_Node $node
	 * @return Varien_Data_Tree_Node|null
	 */
	private function getActiveLeaf(Varien_Data_Tree_Node $node) {
		/** @var Varien_Data_Tree_Node|null $result */
		$result = $node;
		foreach ($node->getChildren() as $child) {
			/** @var Varien_Data_Tree_Node $child */
			// для сокращения количества вызовов findActiveNode()
			if ($child->getIsActive()) {
				$result = $this->getActiveLeaf($child);
				break;
			}
		}
		return $result;
	}

	/**
	 * @param Varien_Data_Tree_Node $menuTree
	 * @return string[]
	 */
	private function getActiveNodePath(Varien_Data_Tree_Node $menuTree) {
		/** @var Varien_Data_Tree_Node|null $activeNode */
		$activeNode = $this->getActiveLeaf($menuTree);
		/** @var string $result */
		$result = [];
		while ($activeNode && $activeNode->getParent()) {
			$result[]= $activeNode->getId();
			$activeNode = $activeNode->getParent();
		}
		return $result;
	}

	/**
	 * @param Varien_Data_Tree_Node $menuTree
	 * @return string
	 */
	private function getJsRm(Varien_Data_Tree_Node  $menuTree) {
		return strtr("
			<script type='text/javascript'>
				rm.namespace('rm.topMenu');
				rm.topMenu.activeNodePath = {json};
			</script>
		", array('{json}' => df_json_encode_js($this->getActiveNodePath($menuTree))));
	}

	/** @return bool */
	private function needCacheRm() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
					df_module_enabled(Df_Core_Module::SPEED)
				&&
					df_cfgr()->speed()->blockCaching()->pageHtmlTopmenu()
			;
		}
		return $this->{__METHOD__};
	}
}