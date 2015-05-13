<?php
/**
 * @method Varien_Data_Tree_Node|null getRoot(Mage_Catalog_Model_Category $parentNodeCategory = null, int $recursionLevel = 3)
 */
class Df_AccessControl_Block_Admin_Tab_Tree	extends Mage_Adminhtml_Block_Catalog_Category_Tree {
	/**
	 * @param int $categoryId
	 * @param int $roleId
	 * @return mixed[][]
	 */
	public function getChildrenNodes($categoryId, $roleId) {
		df_param_integer($categoryId, 0);
		df_param_integer($roleId, 1);
		/** @var mixed[][] $result */
		$result = array();
		/** @var Varien_Data_Tree $tree */
		$tree = $this->getRoot(Df_Catalog_Model_Category::ld($categoryId), $storeId = 1)->getTree();
		/** @var Varien_Data_Tree_Node|null $node */
		$node = $tree->getNodeById($categoryId);
		if ($node && $node->hasChildren()) {
			foreach ($node->getChildren() as $child) {
				/** Varien_Data_Tree_Node $node */
				$result[]= $this->_getNodeJson($child);
			}
		}
		return $result;
	}

	/**
	 * @override
	 * @param bool|null $expanded[optional]
	 * @return string
	 */
	public function getLoadTreeUrl($expanded = null) {
		return $this->getUrl('df_access_control/admin/categories', array('rid' => $this->getRoleId()));
	}

	/** @return string */
	public function getSelectedCategoriesAsString() {
		// Результат может быть пустой строкой!
		return implode(',', $this->getSelectedCategories());
	}

	/**
	 * @override
	 * @return string|null
	 */
	public function getTemplate() {
		return
			!(df_enabled(Df_Core_Feature::ACCESS_CONTROL) && df_cfg()->admin()->access_control()->getEnabled())
			? null
			: 'df/access_control/tab/tree.phtml'
		;
	}

	/** @return bool */
	public function isRootVisible() {
		return !is_null($this->getRoot()) && $this->getRoot()->getDataUsingMethod('is_visible');
	}

	/** @return bool */
	public function isTreeEmpty() {return !$this->getRoot() || !$this->getRoot()->hasChildren();}

	/**
	 * Get JSON of a tree node or an associative array
	 * @override
	 * @param Varien_Data_Tree_Node|array $node
	 * @param int $level[optional]
	 * @return mixed[]
	 */
	protected function _getNodeJson($node, $level = 1) {
		if (is_array($node)) {
			$node = new Varien_Data_Tree_Node ($node, 'entity_id', new Varien_Data_Tree);
		}
		df_assert($node instanceof Varien_Data_Tree_Node);
		df_param_integer($level, 1);
		/** @var mixed[] $result */
		$result = parent::_getNodeJson($node, $level);
		/** @var bool $needBeChecked */
		$needBeChecked = in_array($node->getId(), $this->getSelectedCategories());
		if ($needBeChecked) {
			$result['checked'] = true;
		}
		if ($this->_isParentSelectedCategory($node) || $needBeChecked) {
			$result['expanded'] = true;
		}
		return $result;
	}

	/** @return Df_AccessControl_Model_Role */
	private function getRole() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_AccessControl_Model_Role::i();
			// Обратите внимание,
			// что объект Df_AccessControl_Model_Role может отсутствовать в БД.
			// Видимо, это дефект моего программирования 2011 года.
			$this->{__METHOD__}->load($this->getRoleId());
		}
		return $this->{__METHOD__};
	}

	/** @return int|null */
	private function getRoleId() {return df_request('rid');}

	/** @return int[] */
	private function getSelectedCategories() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				!$this->getRole()->isModuleEnabled()
				? array()
				: $this->getRole()->getCategoryIds()
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_AccessControl_Block_Admin_Tab_Tree */
	public static function i() {return df_block(__CLASS__);}
}