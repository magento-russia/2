<?php
class Df_Cms_Model_Resource_Hierarchy_Node extends Df_Core_Model_Resource {
	/**
	 * Check identifier
	 *
	 * If a CMS Page belongs to a tree (binded to a tree node), it should not be accessed standalone
	 * only by URL that identifies it in a hierarchy.
	 *
	 * @param string $identifier
	 * @param int $storeId
	 * @return bool
	 */
	public function checkIdentifier($identifier, $storeId) {
		$adapter = $this->_getReadAdapter();
		$select  = $adapter->select()
			->from(array('main_table' => df_table('cms/page')), array('page_id', 'website_root'))
			->join(
				array('cps' => df_table('cms/page_store')),'main_table.page_id = `cps`.page_id', null)
			->where('main_table.identifier = ?', $identifier)
			->where('main_table.is_active=1 AND `cps`.store_id in (0, ?) ', $storeId)
			->order('store_id DESC')
			->limit(1);
		$page = $adapter->fetchRow($select);
		if (!$page || $page['website_root'] == 1) {
			return false;
		}
		return true;
	}

	/**
	 * @used-by Df_Cms_Observer::cms_page_save_before()
	 * @param int $pageId
	 * @return void
	 */
	public function deleteNodesByPageId($pageId) {
		df_table_delete($this->getMainTable(), 'page_id', $pageId);
	}

	/**
	 * @param int $pageId
	 * @return Df_Cms_Model_Resource_Hierarchy_Node
	 */
	public function deleteRootNodesByPageId($pageId) {
		$this->_getWriteAdapter()->delete(
			$this->getMainTable()
			, df_db_quote_into('(? = page_id) AND (parent_node_id IS null)', $pageId)
		);
		return $this;
	}

	/**
	 * Remove nodes defined by id.
	 * Which will also remove their child nodes by foreign key.
	 * @used-by Df_Cms_Model_Hierarchy_Node::collectTree()
	 * @param int|int[] $nodeIds
	 * @return void
	 */
	public function dropNodes($nodeIds) {df_table_delete($this->getMainTable(), 'node_id', $nodeIds);}

	/**
	 * Retrieve tree meta data flags from secondary table.
	 * Filtering by root node of passed node.
	 *
	 * @param Df_Cms_Model_Hierarchy_Node $object
	 * @return array
	 */
	public function getTreeMetaData(Df_Cms_Model_Hierarchy_Node $object) {
		$read = $this->_getReadAdapter();
		$select = $read->select();
		$select
			->from(df_table(self::TABLE_META_DATA))
			->where('node_id = ?', df_first(df_explode_xpath($object->getXpath())))
		;
		return $read->fetchRow($select);
	}

	/**
	 * Retrieve brief/detailed Tree Slice for object
	 * 2 level array
	 *
	 * @param Df_Cms_Model_Hierarchy_Node $object
	 * @param int $up, if equals zero - no limitation
	 * @param int $down, if equals zero - no limitation
	 * @return array
	 */
	public function getTreeSlice($object, $up = 0, $down = 0) {
		$tree	   = array();
		$parentId   = $object->getParentNodeId();
		if ($this->_treeMaxDepth > 0 && $object->getLevel() > $this->_treeMaxDepth) {
			return $tree;
		}
		$xpath = df_explode_xpath($object->getXpath());
		if (!$this->_treeIsBrief) {
			array_pop($xpath); //remove self node
		}
		$parentIds = array();
		$useUp = $up > 0;
		while (count($xpath) > 0) {
			if ($useUp && $up == 0) {
				break;
			}
			$parentIds[]= array_pop($xpath);
			if ($useUp) {
				$up--;
			}
		}

		/**
		 * Collect childs
		 */
		$children = array();
		if ($this->_treeMaxDepth > 0 && $this->_treeMaxDepth > $object->getLevel() || $this->_treeMaxDepth == 0) {
			$children = $this->_getSliceChildren($object, $down);
		}

		/**
		 * Collect parent and neighbours
		 */
		if ($parentIds) {
			$parentId = $parentIds[count($parentIds) -1];
			if ($this->_treeIsBrief) {
				$where = df_db_quote_into($this->getMainTable().'.node_id IN (?)', $parentIds);
				// Collect neighbours if there are no children
				if (count($children) == 0) {
					$where.= df_db_quote_into(' OR parent_node_id=?', $object->getParentNodeId());
				}
			} else {
				$where = df_db_quote_into('parent_node_id IN (?) OR parent_node_id IS null', $parentIds);
			}
		} else {
			$where = 'parent_node_id IS null';
		}

		$select = $this->_getLoadSelectWithoutWhere()
			->where($where)
			->order(array('level', $this->getMainTable().'.sort_order'));
		$nodes = $select->query()->fetchAll();
		$tree = $this->_prepareRelatedStructure($nodes, 0, $tree);
		// add children to tree
		if (count($children) > 0) {
			$tree = $this->_prepareRelatedStructure($children, 0, $tree);
		}
		return $tree;
	}

	/**
	 * Retrieve xpaths array which contains defined page
	 *
	 * @param int $pageId
	 * @return array
	 */
	public function getTreeXpathsByPage($pageId) {
		$select = df_select()
			->from($this->getMainTable(), 'xpath')
			->where('? = page_id', $pageId)
		;
		$rows = df_conn()->fetchAll($select);
		return array_column($rows, 'xpath');
	}

	/**
	 * Load node by Request Path
	 *
	 * @param Df_Cms_Model_Hierarchy_Node $object
	 * @param string $url
	 * @return Df_Cms_Model_Resource_Hierarchy_Node
	 */
	public function loadByRequestUrl($object, $url)
	{
		$read = $this->_getReadAdapter();
		if ($read && !is_null($url)) {
			$select = $this->_getLoadSelect('request_url', $url, $object);
			$data = $read->fetchRow($select);
			if ($data) {
				$object->setData($data);
			}
		}
		$this->_afterLoad($object);
		return $this;
	}

	/**
	 * Load First node by parent node id
	 *
	 * @param Df_Cms_Model_Hierarchy_Node $object
	 * @param int $parentNodeId
	 * @return Df_Cms_Model_Resource_Hierarchy_Node
	 */
	public function loadFirstChildByParent($object, $parentNodeId)
	{
		$read = $this->_getReadAdapter();
		if ($read && !is_null($parentNodeId)) {
			$select = $this->_getLoadSelect('parent_node_id', $parentNodeId, $object)
				->order(array($this->getMainTable().'.sort_order'))
				->limit(1);
			$data = $read->fetchRow($select);
			if ($data) {
				$object->setData($data);
			}
		}
		$this->_afterLoad($object);
		return $this;
	}

	/**
	 * Saving meta if such available for node (in case node is root node of three)
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return Df_Cms_Model_Resource_Hierarchy_Node
	 */
	public function saveMetaData(Mage_Core_Model_Abstract $object)
	{
		// we save to metadata table not only metadata :(
		//if ($object->getParentNodeId()) {
		//	return $this;
		//}
		$preparedData = $this->_prepareDataForTable($object, df_table(self::TABLE_META_DATA));
		$this->_getWriteAdapter()->insertOnDuplicate(
			df_table(self::TABLE_META_DATA), $preparedData, array_keys($preparedData));
		return $this;
	}

	/**
	 * Flag to indicate whether append active pages only or not
	 *
	 * @param bool $flag
	 * @return Df_Cms_Model_Resource_Hierarchy_Node
	 */
	public function setAppendActivePagesOnly($flag) {
		$this->_appendActivePagesOnly = !!$flag;
		return $this;
	}

	/**
	 * Flag to indicate whether append included pages (menu_excluded=0) only or not
	 *
	 * @param bool $flag
	 * @return Df_Cms_Model_Hierarchy_Node
	 */
	public function setAppendIncludedPagesOnly($flag) {
		$this->_appendIncludedPagesOnly = !!$flag;
		return $this;
	}

	/**
	 * Setter for $_treeIsBrief
	 *
	 * @param bool $brief
	 * @return Df_Cms_Model_Resource_Hierarchy_Node
	 */
	public function setTreeIsBrief($brief) {
		$this->_treeIsBrief = !!$brief;
		return $this;
	}

	/**
	 * Setter for $_treeMaxDepth
	 *
	 * @param int $depth
	 * @return Df_Cms_Model_Resource_Hierarchy_Node
	 */
	public function setTreeMaxDepth($depth)
	{
		$this->_treeMaxDepth = (int)$depth;
		return $this;
	}

	/**
	 * Rebuild URL rewrites for a tree with specified path.
	 *
	 * @param string $xpath
	 * @return Df_Cms_Model_Resource_Hierarchy_Node
	 */
	public function updateRequestUrlsForTreeByXpath($xpath)
	{
		$select = $this->_getReadAdapter()->select()
			->from(
				array('node_table' => $this->getMainTable()),array($this->getIdFieldName(), 'parent_node_id', 'page_id', 'identifier', 'request_url'))
			->joinLeft(
				array('page_table' => df_table('cms/page')),'node_table.page_id=page_table.page_id',array(
					'page_identifier' => 'identifier',))
			->where('xpath LIKE ?', $xpath. '/%')
			->orWhere('xpath = ?', $xpath)
			->group('node_table.node_id')
			->order(array('level', 'node_table.sort_order'));
		$nodes	  = array();
		$rowSet	 = $select->query()->fetchAll();
		foreach ($rowSet as $row) {
			$nodes[df_nat0($row['parent_node_id'])][$row[$this->getIdFieldName()]] = $row;
		}
		if (!$nodes) {
			return $this;
		}
		$keys = array_keys($nodes);
		$parentNodeId = array_shift($keys);
		$this->_updateNodeRequestUrls($nodes, $parentNodeId, null);
		return $this;
	}

	/**
	 * Updating nodes sort_order with new value.
	 *
	 * @param int $nodeId
	 * @param int $sortOrder
	 * @return Df_Cms_Model_Resource_Hierarchy_Node
	 */
	public function updateSortOrder($nodeId, $sortOrder)
	{
		$this->_getWriteAdapter()->update($this->getMainTable(),array('sort_order' => $sortOrder),array($this->getIdFieldName() . ' = ? ' => $nodeId));
		return $this;
	}

	/**
	 * Return nearest parent params for pagination/menu
	 *
	 * @param Df_Cms_Model_Hierarchy_Node $object
	 * @param string $fieldName Parent metadata field to use in filter
	 * @param string|string[] $values Values for filter
	 * @return array|null
	 */
	public function getParentMetadataParams($object, $fieldName, $values) {
		$parentIds = preg_split('/\/{1}/', $object->getXpath(), 0, PREG_SPLIT_NO_EMPTY);
		array_pop($parentIds); //remove self node
		$select = $this->_getLoadSelectWithoutWhere()
			->where($this->getMainTable().'.node_id IN (?)', $parentIds)
			->where('metadata_table.'.$fieldName.' IN (?)', df_array($values))
			->order(array($this->getMainTable().'.level DESC'))
			->limit(1);
		$params = $this->_getReadAdapter()->fetchRow($select);
		if (is_array($params) && count($params) > 0) {
			return $params;
		}
		return null;
	}

	/**
	 * Retrieve Parent node children
	 *
	 * @param Df_Cms_Model_Hierarchy_Node $object
	 * @return array
	 */
	public function getParentNodeChildren($object)
	{
		if ($object->getParentNodeId() === null) {
			$where = 'parent_node_id IS null';
		} else {
			$where = df_db_quote_into('parent_node_id=?', $object->getParentNodeId());
		}
		$select = $this->_getLoadSelectWithoutWhere()
			->where($where)
			->order($this->getMainTable().'.sort_order');
		$nodes = $select->query()->fetchAll();
		return $nodes;
	}

	/**
	 * Load page data for model if defined page id
	 *
	 * @param Df_Cms_Model_Hierarchy_Node $object
	 * @return Df_Cms_Model_Resource_Hierarchy_Node
	 */
	public function loadPageData($object)
	{
		$pageId = $object->getPageId();
		if (!empty($pageId)) {
			$columns = array(
				'page_title'		=> 'title','page_identifier'   => 'identifier','page_is_active'	=> 'is_active'
			);
			$select = $this->_getReadAdapter()->select()
				->from(df_table('cms/page'), $columns)
				->where('page_id=?', $pageId)
				->limit(1);
			$row = $this->_getReadAdapter()->fetchRow($select);
			if ($row) {
				$object->addData($row);
			}
		}
		return $this;
	}

	/**
	 * Remove node which are representing specified page from defined nodes.
	 * Which will also remove child nodes by foreign key.
	 *
	 * @param int $pageId
	 * @param int|array $nodes
	 * @return Df_Cms_Model_Resource_Hierarchy_Node
	 */
	public function removePageFromNodes($pageId, $nodes) {
		$whereClause = df_db_quote_into('page_id = ? AND ', $pageId);
		$whereClause .= df_db_quote_into('parent_node_id IN (?)', $nodes);
		$this->_getWriteAdapter()->delete($this->getMainTable(), $whereClause);
		return $this;
	}

	/**
	 * @override
	 * @param Df_Cms_Model_Hierarchy_Node|Mage_Core_Model_Abstract $object
	 * @return Df_Cms_Model_Resource_Hierarchy_Node
	 */
	protected function _afterLoad(Mage_Core_Model_Abstract $object) {
		parent::_afterLoad($object);
		/** @var string|null $additionalSettingsEncoded */
		$additionalSettingsEncoded =
			$object->getData(Df_Cms_Model_Hierarchy_Node::P__ADDITIONAL_SETTINGS)
		;
		if (!is_null($additionalSettingsEncoded)) {
			/** @var array|bool $additionalSettings */
			$additionalSettings = df_json_decode($additionalSettingsEncoded);
			df_assert_array($additionalSettings);
			$object
				->addData(
					$additionalSettings
				)
			;
		}
		return $this;
	}

	/**
	 * @param Df_Cms_Model_Hierarchy_Node|Mage_Core_Model_Abstract $object
	 * @return Df_Cms_Model_Resource_Hierarchy_Node
	 */
	protected function _afterSave(Mage_Core_Model_Abstract $object) {
		if ($object->dataHasChangedFor($this->getIdFieldName())) {
			// update xpath
			$xpath = $object->getXpath() . $object->getId();
			$bind = array('xpath' => $xpath);
			$where = df_db_quote_into($this->getIdFieldName() . '=?', $object->getId());
			$this->_getWriteAdapter()->update($this->getMainTable(), $bind, $where);
			$object->setXpath($xpath);
		}
		return $this;
	}

	/**
	 * Perform actions before object save
	 * @override
	 * @param Df_Cms_Model_Hierarchy_Node|Mage_Core_Model_Abstract $object
	 * @return Mage_Core_Model_Resource_Db_Abstract
	 */
	protected function _beforeSave(Mage_Core_Model_Abstract $object) {
		parent::_beforeSave($object);
		/** @var array $additionalSettings */
		$additionalSettings =
			dfa_select($object->getData(), Df_Cms_Model_Hierarchy_Node::getMetadataKeysAdditional())
		;
		/**
		 * @see Zend_Json::encode() использует
		 * @see json_encode() при наличии расширения PHP JSON
		 * и свой внутренний кодировщик при отсутствии расширения PHP JSON.
		 * http://stackoverflow.com/questions/4402426/json-encode-json-decode-vs-zend-jsonencode-zend-jsondecode
		 * Обратите внимание,
		 * что расширение PHP JSON не входит в системные требования Magento.
		 * http://www.magentocommerce.com/system-requirements
		 * Поэтому использование @see Zend_Json::encode()
		 * выглядит более правильным, чем @see json_encode().
		 */
		$object->setData(
			Df_Cms_Model_Hierarchy_Node::P__ADDITIONAL_SETTINGS
			, Zend_Json::encode($additionalSettings)
		);
		return $this;
	}

	/**
	 * Prepare load select but without where part.
	 * So all extra joins to secondary tables will be present.
	 * @return Zend_Db_Select
	 */
	public function _getLoadSelectWithoutWhere() {
		/** @var Zend_Db_Select $result */
		$result = $this->_getLoadSelect(null, null, null)->reset(Zend_Db_Select::WHERE);
		$this->_applyParamFilters($result);
		return $result;
	}

	/**
	 * Return object nested childs and its neighbours in Tree Slice
	 *
	 * @param Df_Cms_Model_Hierarchy_Node $object
	 * @param int $down Number of Child Node Levels to Include, if equals zero - no limitation
	 * @return mixed[]
	 */
	protected function _getSliceChildren($object, $down = 0) {
		$select = $this->_getLoadSelectWithoutWhere();
		$xpath = $object->getXpath() . '/%';
		$select->where('xpath LIKE ?', $xpath);
		if (max($down, $this->_treeMaxDepth) > 0) {
			$maxLevel = $this->_treeMaxDepth > 0
					  ? min($this->_treeMaxDepth, $object->getLevel() + $down)
					  : $object->getLevel() + $down;
			$select->where('level <= ?', $maxLevel);
		}
		$select->order(array('level', $this->getMainTable().'.sort_order'));
		return $select->query()->fetchAll();
	}

	/**
	 * Recursive update Request URL for node and all it's children
	 *
	 * @param array $nodes
	 * @param int $parentNodeId
	 * @param string $path
	 * @return Df_Cms_Model_Resource_Hierarchy_Node
	 */
	protected function _updateNodeRequestUrls(array $nodes, $parentNodeId = 0, $path = null) {
		foreach ($nodes[$parentNodeId] as $nodeRow) {
			$identifier = $nodeRow['page_id'] ? $nodeRow['page_identifier'] : $nodeRow['identifier'];
			if ($path) {
				$requestUrl = $path . '/' . $identifier;
			} else {
				$route = df_explode_xpath($nodeRow['request_url']);
				array_pop($route);
				$route[]= $identifier;
				$requestUrl = implode('/', $route);
			}
			if ($nodeRow['request_url'] != $requestUrl) {
				$this->_getWriteAdapter()->update(
					$this->getMainTable()
					, array('request_url' => $requestUrl)
					, df_db_quote_into($this->getIdFieldName().'=?', $nodeRow[$this->getIdFieldName()])
				);
			}

			if (isset($nodes[$nodeRow[$this->getIdFieldName()]])) {
				$this->_updateNodeRequestUrls($nodes, $nodeRow[$this->getIdFieldName()], $requestUrl);
			}
		}
		return $this;
	}

	/**
	 * Add attributes filter to select object based on flags
	 *
	 * @param Zend_Db_Select $select Select object instance
	 * @return Df_Cms_Model_Resource_Hierarchy_Node
	 */
	protected function _applyParamFilters($select)
	{
		if ($this->_appendActivePagesOnly) {
			$select->where('page_table.is_active=1 OR ' . $this->getMainTable() . '.page_id IS null');
		}
		if ($this->_appendIncludedPagesOnly) {
			$select->where('metadata_table.menu_excluded=0');
		}
		return $this;
	}

	/**
	 * Retrieve select object for load object data.
	 * Join page information if page assigned.
	 * Join secondary table with meta data for root nodes.
	 *
	 * @param string $field
	 * @param mixed $value
	 * @param Df_Cms_Model_Hierarchy_Node $object
	 * @return Varien_Db_Select
	 */
	protected function _getLoadSelect($field, $value, $object)
	{
		$select = parent::_getLoadSelect($field, $value, $object);
		$select->joinLeft(
			array('page_table' => df_table('cms/page'))
			,$this->getMainTable() . '.page_id = page_table.page_id'
			,array(
				'page_title' => 'title'
				,'page_identifier' => 'identifier'
				,'page_is_active' => 'is_active'
			))
			->joinLeft(
				array('metadata_table' => df_table(self::TABLE_META_DATA))
				,$this->getMainTable() . '.' . $this->getIdFieldName() . ' = metadata_table.node_id'
				,array(
					'pager_visibility','pager_frame','pager_jump'
					,'menu_brief','menu_excluded','menu_levels_down'
					,'menu_ordered','menu_list_type'
				));
		$this->_applyParamFilters($select);
		return $select;
	}

	/**
	 * Preparing array where all nodes grouped in sub arrays by parent id.
	 *
	 * @param array $nodes source node's data
	 * @param int $startNodeId
	 * @param array $tree Initial array which will modified and returned with new data
	 * @return array
	 */
	protected function _prepareRelatedStructure($nodes, $startNodeId, $tree)
	{
		foreach ($nodes as $row) {
			$parentNodeId = (int)$row['parent_node_id'] == $startNodeId ? 0 : $row['parent_node_id'];
			$tree[$parentNodeId][$row[$this->getIdFieldName()]] = $row;
		}
		return $tree;
	}

	/**
	 * Flag to indicate whether append active pages only or not
	 * @var bool
	 */
	protected $_appendActivePagesOnly = false;

	/**
	 * Flag to indicate whether append included in menu pages only or not
	 * @var bool
	 */
	protected $_appendIncludedPagesOnly = false;

	/**
	 * Primary key auto increment flag
	 *
	 * @var bool
	 */
	protected $_isPkAutoIncrement = false;

	/**
	 * Tree Detalization, i.e. brief or detailed
	 * @var bool
	 */
	protected $_treeIsBrief = false;

	/**
	 * Maximum tree depth for tree slice, if equals zero - no limitations
	 * @var int
	 */
	protected $_treeMaxDepth = 0;

	/**
	 * Нельзя вызывать @see parent::_construct(),
	 * потому что это метод в родительском классе — абстрактный.
	 * @see Mage_Core_Model_Resource_Abstract::_construct()
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_init(self::TABLE, Df_Cms_Model_Hierarchy_Node::P__ID);}
	/** used-by Df_Cms_Setup_2_0_0::_process() */
	const TABLE = 'df_cms/hierarchy_node';
	/**
	 * @used-by Df_Cms_Model_Resource_Hierarchy_Node_Collection::joinMetaData()
	 * @used-by Df_Cms_Setup_2_0_0::_process()
	 * @used-by Df_Cms_Setup_2_0_1::_process()
	 */
	const TABLE_META_DATA = 'df_cms/hierarchy_metadata';
	/** @return Df_Cms_Model_Resource_Hierarchy_Node */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}