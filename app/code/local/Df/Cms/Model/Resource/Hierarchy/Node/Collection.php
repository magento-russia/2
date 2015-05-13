<?php
class Df_Cms_Model_Resource_Hierarchy_Node_Collection
	extends Mage_Core_Model_Mysql4_Collection_Abstract {
	/**
	 * Adding sub query for custom column to determine on which stores page active.
	 * @return Df_Cms_Model_Resource_Hierarchy_Node_Collection
	 */
	public function addCmsPageInStoresColumn() {
		$this->_needAddStoresColumn = true;
		return $this;
	}
	/** @var bool */
	private $_needAddStoresColumn = false;

	/**
	 * Adds dynamic column with maximum value (which means that it
	 * is sort_order of last direct child) of sort_order column in scope of one node.
	 * @return Df_Cms_Model_Resource_Hierarchy_Node_Collection
	 */
	public function addLastChildSortOrderColumn() {
		if (!$this->getFlag('last_child_sort_order_column_added')) {
			$subSelect = $this->getConnection()->select();
			$subSelect->from($this->getResource()->getMainTable(), new Zend_Db_Expr('MAX(sort_order)'))
				->where('parent_node_id = `main_table`.`node_id`');
			$this->getSelect()->columns(array('last_child_sort_order' => $subSelect));
			$this->setFlag('last_child_sort_order_column_added', true);
		}
		return $this;
	}

	/**
	 * Apply filter to retrieve only root nodes.
	 *
	 * @param int $pageId
	 * @return Df_Cms_Model_Resource_Hierarchy_Node_Collection
	 */
	public function addPageFilter($pageId) {
		$this->addFieldToFilter('page_id', $pageId);
		return $this;
	}

	/**
	 * Apply filter to retrieve only root nodes.
	 * @return Df_Cms_Model_Resource_Hierarchy_Node_Collection
	 */
	public function addRootNodeFilter() {
		$this->addFieldToFilter('parent_node_id', array('null' => true));
		return $this;
	}

	/**
	 * Add Store Filter to assigned CMS pages
	 *
	 * @param int|Mage_Core_Model_Store $store
	 * @param bool $withAdmin Include admin store or not
	 * @return Df_Cms_Model_Resource_Hierarchy_Node_Collection
	 */
	public function addStoreFilter($store, $withAdmin = true) {
		$this->addCmsPageInStoresColumn();
		$this->_storeForFilter = Mage::app()->getStore($store);
		$this->_needAddAdminStoreToFilter = $withAdmin;
		return $this;
	}
	/** @var Mage_Core_Model_Store */
	private $_storeForFilter;
	/** @var bool */
	private $_needAddAdminStoreToFilter = false;

	/**
	 * Apply filter to retrieve nodes with ids which
	 * were defined as parameter or nodes which contain
	 * defined page in their direct children.
	 *
	 * @param int|array $nodeIds
	 * @param int|Mage_Cms_Model_Page|null $page
	 * @return Df_Cms_Model_Resource_Hierarchy_Node_Collection
	 */
	public function applyPageExistsOrNodeIdFilter($nodeIds, $page = null) {
		if (!$this->getFlag('page_exists_or_node_id_filter_applied')) {
			if (!$this->getFlag('page_exists_joined')) {
				$this->joinPageExistsNodeInfo($page);
			}
			$whereExpr = new Zend_Db_Expr(
				rm_quote_into('clone.node_id IS NOT null OR main_table.node_id IN (?)', $nodeIds)
			);
			$this->getSelect()->where($whereExpr);
			$this->setFlag('page_exists_or_node_id_filter_applied', true);
		}
		return $this;
	}

	/** @return Df_Cms_Model_Resource_Hierarchy_Node_Collection */
	public function filterExcludedPagesOut() {
		$this->addFieldToFilter('metadata_table.menu_excluded', 0);
		return $this;
	}

	/** @return Df_Cms_Model_Resource_Hierarchy_Node_Collection */
	public function filterUnpublishedPagesOut() {
		$this->getSelect()
			->where('page_table.is_active=1 OR main_table.page_id IS null')
		;
		return $this;
	}

	/**
	 * Join Cms Page data to collection
	 * @return Df_Cms_Model_Resource_Hierarchy_Node_Collection
	 */
	public function joinCmsPage() {
		if (!$this->getFlag('cms_page_data_joined')) {
			$this->getSelect()->joinLeft(
				array('page_table' => rm_table('cms/page'))
				,'main_table.page_id = page_table.page_id'
				,array(
					'page_title' => 'title'
					,'page_identifier' => 'identifier'
				)
			);
			$this->setFlag('cms_page_data_joined', true);
		}
		return $this;
	}

	/**
	 * Join meta data for tree root nodes from extra table.
	 * @return Df_Cms_Model_Resource_Hierarchy_Node_Collection
	 */
	public function joinMetaData() {
		if (!$this->getFlag('meta_data_joined')) {
			$this->getSelect()->joinLeft(
				array('metadata_table' => rm_table('df_cms/hierarchy_metadata'))
				,'main_table.node_id = metadata_table.node_id'
				,array(
					'pager_visibility'
					,'pager_frame'
					,'pager_jump'
					,'menu_brief'
					,'menu_excluded'
					,'menu_levels_down'
					,'menu_ordered'
					,'menu_list_type'
					,'additional_settings'
				));
		}
		$this->setFlag('meta_data_joined', true);
		return $this;
	}

	/**
	 * Join main table on self to discover which nodes
	 * have defined page as direct child node.
	 *
	 * @param int|Mage_Cms_Model_Page $page
	 * @return Df_Cms_Model_Resource_Hierarchy_Node_Collection
	 */
	public function joinPageExistsNodeInfo($page) {
		if (!$this->getFlag('page_exists_joined')) {
			if ($page instanceof Mage_Cms_Model_Page) {
				$page = $page->getId();
			}
			$onClause = 'main_table.node_id = clone.parent_node_id AND clone.page_id = ?';
			$ifPageExistExpr = new Zend_Db_Expr('IF(clone.node_id is null, 0, 1)');
			$ifCurrentPageExpr = new Zend_Db_Expr(
				rm_quote_into('IF(main_table.page_id = ?, 1, 0)', $page)
			);
			$this->getSelect()->joinLeft(
				array('clone' => $this->getResource()->getMainTable())
				,rm_quote_into($onClause, $page)
				,array('page_exists' => $ifPageExistExpr, 'current_page' => $ifCurrentPageExpr)
			);
			$this->setFlag('page_exists_joined', true);
		}
		return $this;
	}

	/**
	 * Order tree by level and position
	 * @return Df_Cms_Model_Resource_Hierarchy_Node_Collection
	 */
	public function setOrderByLevel() {
		$this->getSelect()->order(array('level', 'sort_order'));
		return $this;
	}

	/**
	 * Order nodes as tree
	 * @return Df_Cms_Model_Resource_Hierarchy_Node_Collection
	 */
	public function setTreeOrder() {
		if (!$this->getFlag('tree_order_added')) {
			$this->getSelect()
				->order(
					array(
						'level'
						,'main_table.sort_order'
					)
				)
			;
			$this->setFlag('tree_order_added', true);
		}
		return $this;
	}

	/**
	 * @override
	 * @return Df_Cms_Model_Resource_Hierarchy_Node_Collection
	 */
	protected function _afterLoad() {
		foreach ($this->_items as $item) {
			/** @var Mage_Core_Model_Abstract $item */
			/**
			 * Странно, что стандартный код этого не делает
			 */
			$item->afterLoad();
		}
		parent::_afterLoad();
		return $this;
	}

	/**
	 * @override
	 * @return Df_Cms_Model_Resource_Hierarchy_Node_Collection
	 */
	protected function _renderFiltersBefore() {
		parent::_renderFiltersBefore();
		if ($this->_needAddStoresColumn) {
			$selectStores =
				$this->getConnection()->select()
					->from(
						array('store' => rm_table('cms/page_store'))
						,new Zend_Db_Expr('GROUP_CONCAT(`store_id`)')
					)
					->where('store.page_id = main_table.page_id')
			;
			if (!is_null($this->_storeForFilter)) {
				$selectStores
					->where(
						'store.store_id in (?)'
						,$this->_needAddAdminStoreToFilter
						?
							array(0, $this->_storeForFilter->getId())
						:
							$this->_storeForFilter->getId()
					)
				;
				$this->getSelect()->having('main_table.page_id IS null OR page_in_stores IS NOT null');
			}
			$this->getSelect()
				->columns(
					array(
						  'page_in_stores' => new Zend_Db_Expr('(' . $selectStores . ')')
					)
				)
			;
		}
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Cms_Model_Hierarchy_Node::mf(), Df_Cms_Model_Resource_Hierarchy_Node::mf());
	}
	const _CLASS = __CLASS__;
	/** @return Df_Cms_Model_Resource_Hierarchy_Node_Collection */
	public static function i() {return new self;}
}