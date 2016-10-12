<?php
class Df_Catalog_Model_Processor_Menu extends Df_Core_Model {
	/** @return void */
	public function process() {
		$this->initActiveStatus($this->getRoot());
		foreach ($this->getRoot()->getChildren() as $rootCategoryNode) {
			/** @var Varien_Data_Tree_Node $rootCategoryNode */
			$this->getMenu()->addChild($rootCategoryNode);
		}
	}

	/** @return string[] */
	protected function getPropertiesToCachePerStore() {return array('_root');}

	/**
	 * @param Varien_Data_Tree_Node $parent
	 * @param Varien_Data_Tree_Node_Collection|Varien_Data_Tree_Node[] $categories
	 * @return void
	 */
	private function addCategories(Varien_Data_Tree_Node $parent, $categories) {
		foreach ($categories as $category) {
			/** @var Varien_Data_Tree_Node $category */
			if ($category->getIsActive()) {
				$this->addCategory($parent, $category);
			}
		}
	}

	/**
	 * @param Varien_Data_Tree_Node $parent
	 * @param Varien_Data_Tree_Node|Df_Catalog_Model_Category|Varien_Object $category
	 * @return void
	 */
	private function addCategory(Varien_Data_Tree_Node $parent, Varien_Object $category) {
		/** @var Varien_Data_Tree_Node $node */
		$node =
			new Varien_Data_Tree_Node(
				$data = array(
					'name' => $category->getName()
					,'id' => 'category-node-' . $category->getId()
					,'url' => df_mage()->catalog()->categoryHelper()->getCategoryUrl($category)
					,self::$NODE__CATEGORY_ID => $category->getId()
				)
				,$idField = 'id'
				,$parent->getTree()
				,$parent
			)
		;
		$parent->addChild($node);
		$this->addCategories(
			$node
			, $this->isFlat() ? (array)$category->getData('children_nodes') : $category->getChildren()
		);
	}

	/** @return Varien_Data_Tree_Node */
	private function getMenu() {return $this->cfg(self::P__MENU);}

	/** @return Varien_Data_Tree_Node */
	private function getRoot() {
		if (!isset($this->_root)) {
			/** @var Varien_Data_Tree_Node $result */
			/** @var Varien_Data_Tree $tree */
			$tree = new Varien_Data_Tree();
			/** @var Varien_Data_Tree_Node $result */
			$result =
				new Varien_Data_Tree_Node(
					$data = array(
						'name' => 'rm-catalog-root'
						,'is_active' => false
						,'id' => 'rm-catalog-root'
						,'url' => ''
					)
					,$idField = 'id'
					,$tree
					,$parent = null
				)
			;
			$this->addCategories($result, df_mage()->catalog()->categoryHelper()->getStoreCategories());
			$this->_root = $result;
		}
		return $this->_root;
	}
	/** @var Varien_Data_Tree_Node */
	protected $_root;

	/**
	 * @param Varien_Data_Tree_Node $node
	 * @return void
	 */
	private function initActiveStatus(Varien_Data_Tree_Node $node) {
		$node->setData('is_active', $this->isActive($node));
		foreach ($node->getChildren() as $child) {
			/** @var Varien_Data_Tree_Node $child */
			$this->initActiveStatus($child);
		}
	}

	/**
	 * @param Varien_Data_Tree_Node $node
	 * @return bool
	 */
	private function isActive(Varien_Data_Tree_Node $node) {
		/** @var array(int => int) $map */
		static $map;
		if (!isset($map)) {
			$map =
				!rm_state()->hasCategory()
				? array()
				: array_flip(
					array_map(
						'intval'
						, explode(',', rm_state()->getCurrentCategory()->getPathInStore())
					)
				)
			;
		}
		$result = isset($map[intval($node->getData(self::$NODE__CATEGORY_ID))]);
		return $result;
	}

	/** @return bool */
	private function isFlat() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_mage()->catalog()->category()->flatHelper()->isEnabled();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__MENU, 'Varien_Data_Tree_Node');
	}
	const P__MENU = 'menu';

	/** @var string */
	private static $NODE__CATEGORY_ID = 'category_id';

	/**
	 * @param Varien_Data_Tree_Node $menu
	 * @return Df_Catalog_Model_Processor_Menu
	 */
	public static function i(Varien_Data_Tree_Node $menu) {return new self(array(self::P__MENU => $menu));}
}