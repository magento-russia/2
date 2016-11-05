<?php
/**
 * @used-by Df_AccessControl_AdminController::categoriesAction()
 * @used-by Df_AccessControl_Block_Admin_Tab::renderCategoryTree()
 * @method Varien_Data_Tree_Node|null getRoot(Df_Catalog_Model_Category $parentNodeCategory = null, int $recursionLevel = 3)
 */
class Df_AccessControl_Block_Admin_Tab_Tree	extends Mage_Adminhtml_Block_Catalog_Category_Tree {
	/**
	 * @override
	 * @see Mage_Adminhtml_Block_Catalog_Category_Tree::getLoadTreeUrl()
	 * @used-by app/design/adminhtml/rm/default/template/df/access_control/tab/tree.phtml
	 * @param bool|null $expanded [optional]
	 * @return string
	 */
	public function getLoadTreeUrl($expanded = null) {
		return $this->getUrl('df_access_control/admin/categories', array('rid' => $this->roleId()));
	}

	/**
	 * @override
	 * @see Mage_Core_Block_Template::getTemplate()
	 * @used-by Mage_Core_Block_Template::_toHtml()
	 * @used-by Mage_Core_Block_Template::getCacheKeyInfo()
	 * @used-by Mage_Core_Block_Template::getTemplateFile()
	 * @return string|null
	 */
	public function getTemplate() {return
		!Df_AccessControl_Settings::s()->getEnabled()
		? null
		:'df/access_control/tab/tree.phtml'
	;}

	/** @return bool */
	public function isTreeEmpty() {return !$this->getRoot() || !$this->getRoot()->hasChildren();}

	/**
	 * @override
	 * @see Mage_Adminhtml_Block_Catalog_Category_Tree::_getNodeJson()
	 * @used-by getChildrenNodes()
	 * @used-by Mage_Adminhtml_Block_Catalog_Category_Tree::getBreadcrumbsJavascript()
	 * @used-by Mage_Adminhtml_Block_Catalog_Category_Tree::getTree()
	 * @used-by Mage_Adminhtml_Block_Catalog_Category_Tree::getTreeJson()
	 * @param Varien_Data_Tree_Node|array $node
	 * @param int $level [optional]
	 * @return array(string => mixed)
	 */
	protected function _getNodeJson($node, $level = 1) {
		if (is_array($node)) {
			$node = new Varien_Data_Tree_Node($node, 'entity_id', new Varien_Data_Tree);
		}
		df_assert($node instanceof Varien_Data_Tree_Node);
		df_param_integer($level, 1);
		/** @var mixed[] $result */
		$result = parent::_getNodeJson($node, $level);
		/** @var bool $needBeChecked */
		$needBeChecked = in_array($node->getId(), $this->selectedCategories());
		if ($needBeChecked) {
			$result['checked'] = true;
		}
		if ($this->_isParentSelectedCategory($node) || $needBeChecked) {
			$result['expanded'] = true;
		}
		return $result;
	}

	/**
	 * @used-by app/design/adminhtml/rm/default/template/df/access_control/tab/tree.phtml
	 * @return bool
	 */
	protected function isRootVisible() {
		return $this->getRoot() && $this->getRoot()->getData('is_visible');
	}

	/**
	 * @used-by _getNodeJson()
	 * @used-by selectedCategoriesS()
	 * @used-by app/design/adminhtml/rm/default/template/df/access_control/tab/tree.phtml
	 * @return int[]
	 */
	protected function selectedCategories() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				!$this->roleId()
				? array()
				: Df_AccessControl_Model_Role::ld($this->roleId())->getCategoryIds()
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Метод вернт null в сценарии создания роли (когда роль ещё не сохранена в БД).
	 * @used-by selectedCategories()
	 * @used-by getLoadTreeUrl()
	 * @return int|null
	 */
	private function roleId() {return df_request('rid');}

	/**
	 * @used-by Df_AccessControl_AdminController::getChildrenNodes()
	 * @param int $categoryId
	 * @return array(array(string => mixed))
	 */
	public static function getChildrenNodes($categoryId) {
		/** @var array(array(string => mixed)) $result */
		$result = [];
		/** @var Df_AccessControl_Block_Admin_Tab_Tree $i */
		$i = new self;
		/** @var Varien_Data_Tree $tree */
		$tree = $i->getRoot(Df_Catalog_Model_Category::ld($categoryId), $storeId = 1)->getTree();
		/** @var Varien_Data_Tree_Node|null $node */
		/** @noinspection PhpParamsInspection */
		$node = $tree->getNodeById($categoryId);
		if ($node) {
			foreach ($node->getChildren() as $child) {
				/** Varien_Data_Tree_Node $child */
				$result[]= $i->_getNodeJson($child);
			}
		}
		return $result;
	}
}