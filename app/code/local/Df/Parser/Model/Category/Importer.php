<?php
class Df_Parser_Model_Category_Importer extends Df_Core_Model {
	/** @return Df_Parser_Model_Category_Importer */
	public function import() {
		foreach ($this->getTree()->getNodes() as $category) {
			/** @var Df_Parser_Model_Category_Node $category */
			if (is_null($category->getParent())) {
				$this->importCategory($category);
			}
		}
		return $this;
	}

	/** @return Mage_Core_Model_Store */
	private function getStore() {
		return $this->cfg(self::P__STORE);
	}

	/** @return Df_Parser_Model_Category_Tree */
	private function getTree() {
		return $this->cfg(self::P__TREE);
	}

	/**
	 * @param Df_Parser_Model_Category_Node $nodeCategory
	 * @param Df_Catalog_Model_Category|null $parent [optional]
	 * @return Df_Parser_Model_Category_Importer
	 */
	private function importCategory(Df_Parser_Model_Category_Node $nodeCategory, $parent = null) {
		if (is_null($parent)) {
			$parent = Df_Catalog_Model_Category::ld($this->getStore()->getRootCategoryId());
		}
		/** @var Df_Catalog_Model_Category $category */
		$category =
			Df_Catalog_Model_Category::createAndSave(
				array(
					Df_Catalog_Model_Category::P__PATH =>
						$parent->getDataUsingMethod(Df_Catalog_Model_Category::P__PATH)
					,Df_Catalog_Model_Category::P__NAME => $nodeCategory->getName()
					,Df_Catalog_Model_Category::P__IS_ACTIVE => true
					,Df_Catalog_Model_Category::P__IS_ANCHOR => true
					,Df_Catalog_Model_Category::P__DISPLAY_MODE =>
						Mage_Catalog_Model_Category::DM_MIXED
					,Df_Catalog_Model_Category::P__EXTERNAL_URL =>
						$nodeCategory->getUri()->getUri()
					,'attribute_set_id' =>
						Df_Catalog_Model_Resource_Installer_Attribute::s()->getCategoryAttributeSetId()
					,'include_in_menu' => 1
				)
				,$this->getStore()->getId()
			)
		;
		foreach ($nodeCategory->getChildren() as $children) {
			/** @var Df_Parser_Model_Category_Node $children */
			$this->importCategory($children, $parent = $category);
		}
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__TREE, Df_Parser_Model_Category_Tree::_CLASS)
			->_prop(self::P__STORE, 'Mage_Core_Model_Store')
		;
	}
	const _CLASS = __CLASS__;
	const P__STORE = 'store';
	const P__TREE = 'tree';
	/**
	 * @static
	 * @param Df_Parser_Model_Category_Tree $tree
	 * @param Mage_Core_Model_Store $store
	 * @return Df_Parser_Model_Category_Importer
	 */
	public static function i(Df_Parser_Model_Category_Tree $tree, Mage_Core_Model_Store $store) {
		return new self(array(self::P__TREE => $tree, self::P__STORE => $store));
	}
}