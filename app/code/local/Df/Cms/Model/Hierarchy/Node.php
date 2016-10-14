<?php
/**
 * @method bool|null getPageExists()
 * @method int|null getPageId()
 * @method Df_Cms_Model_Resource_Hierarchy_Node getResource()
 * @method Df_Cms_Model_Hierarchy_Node setIsCurrent(bool $value)
 * @method Df_Cms_Model_Hierarchy_Node setPageNumber(int $value)
 */
class Df_Cms_Model_Hierarchy_Node extends Df_Core_Model {
	/**
	 * Appending passed page as child node for specified nodes and set it specified sort order.
	 * Parent nodes specified as array(parentNodeId => sortOrder)
	 *
	 * @param Mage_Cms_Model_Page $page
	 * @param array $nodes
	 * @return Df_Cms_Model_Hierarchy_Node
	 */
	public function appendPageToNodes($page, $nodes)
	{
		/** @var Df_Cms_Model_Resource_Hierarchy_Node_Collection $parentNodes */
		$parentNodes = $this->getCollection()
			->joinPageExistsNodeInfo($page)
			->applyPageExistsOrNodeIdFilter(array_keys($nodes), $page);
		$pageData = array(
			'page_id' => $page->getId()
			,'identifier' => null
			,'label' => null
		);
		$removeFromNodes = array();
		foreach ($parentNodes as $node) {
			/* @var $node Df_Cms_Model_Hierarchy_Node */
			if (isset($nodes[$node->getId()])) {
				$sortOrder = $nodes[$node->getId()];
				if ($node->getPageExists()) {
					continue;
				}
				else {
					$node->addData($pageData)
						->setParentNodeId($node->getId())
						->unsetData($this->getIdFieldName())
						->setLevel($node->getLevel() + 1)
						->setSortOrder($sortOrder)
						->setRequestUrl($node->getRequestUrl() . '/' . $page->getIdentifier())
						->setXpath($node->getXpath() . '/')
						->save();
				}
			}
			else {
				$removeFromNodes[]= $node->getId();
			}
		}

		if (!empty($removeFromNodes)) {
			$this->getResource()->removePageFromNodes($page->getId(), $removeFromNodes);
		}
		return $this;
	}

	/**
	 * Check identifier
	 *
	 * If a CMS Page belongs to a tree (binded to a tree node), it should not be accessed standalone
	 * only by URL that identifies it in a hierarchy.
	 *
	 * Return true if a page binded to a tree node
	 *
	 * @param string $identifier
	 * @param Df_Core_Model_StoreM|int|string|bool|null $storeId [optional]
	 * @return bool
	 */
	public function checkIdentifier($identifier, $storeId = null) {
		return $this->getResource()->checkIdentifier($identifier, rm_store_id($storeId));
	}

	/**
	 * Collect and save tree
	 *
	 * @param array $data	   modified nodes data array
	 * @param int[] $remove	 the removed node ids
	 * @return Df_Cms_Model_Hierarchy_Node
	 */
	public function collectTree($data, $remove) {
		if (!is_array($data)) {
			return $this;
		}
		$nodes = array();
		foreach ($data as $v) {
			$required = array(
				'node_id', 'parent_node_id', 'page_id', 'label', 'identifier', 'level', 'sort_order'
			);
			// validate required node data
			foreach ($required as $field) {
				if (!array_key_exists($field, $v)) {
					Mage::throwException(df_h()->cms()->__('Invalid node data'));
				}
			}
			// В данной точке программы $parentNodeId не должно принимать значение null,
			// потому что чуть ниже значение $parentNodeId применяется в качестве ключа массива.
			$parentNodeId = empty($v['parent_node_id']) ? 0 : $v['parent_node_id'];
			$pageId = empty($v['page_id']) ? null : df_nat0($v['page_id']);
			$_node = array(
				'node_id' => mb_strpos($v['node_id'], '_') === 0 ? null : df_nat0($v['node_id'])
				,'page_id' => $pageId
				,'label' => !$pageId ? $v['label'] : null
				,'identifier' => !$pageId ? $v['identifier'] : null
				,'level' => df_int($v['level'])
				,'sort_order' => df_int($v['sort_order'])
				,'request_url' => $v['identifier']
			);
			$nodes[$parentNodeId][$v['node_id']] =
				Df_Cms_Helper_Hierarchy::s()->copyMetaData($v, $_node)
			;
		}
		$this->getResource()->beginTransaction();
		try {
			// remove deleted nodes
			if (!empty($remove)) {
				$this->getResource()->dropNodes($remove);
			}
			// recursive node save
			$this->_collectTree($nodes, $this->getId(), $this->getRequestUrl(), $this->getId(), 0);
			$this->getResource()->commit();
		} catch (Exception $e) {
			$this->getResource()->rollBack();
			df_error($e);
		}
		return $this;
	}

	/**
	 * Retrieve Node or Page identifier
	 * @return string
	 */
	public function getIdentifier()
	{
		$identifier = $this->_getData('identifier');
		if (is_null($identifier)) {
			$identifier = $this->_getData('page_identifier');
		}
		return $identifier;
	}

	/**
	 * Retrieve Node label or Page title
	 * @return string
	 */
	public function getLabel() {
		$label = $this->_getData('label');
		if (is_null($label)) {
			$label = $this->_getData('page_title');
		}
		return $label;
	}

	/** @return int */
	public function getLevel() {return $this->cfg(self::P__LEVEL);}

	/** @return int */
	public function getMenuLevelsDown() {return $this->cfg(self::P__MENU_LEVELS_DOWN, 0);}

	/**
	 * Return nearest parent params for node pagination
	 * @return array|null
	 */
	public function getMetadataPagerParams()
	{
		$values = array(
			Df_Cms_Helper_Hierarchy::METADATA_VISIBILITY_YES,Df_Cms_Helper_Hierarchy::METADATA_VISIBILITY_NO);
		return $this->getResource()->getParentMetadataParams($this, 'pager_visibility', $values);
	}

	/** @return string|null */
	public function getPageIdentifier() {return $this->cfg(self::P__PAGE_IDENTIFIER);}

	/** @return string */
	public function getPageInStores() {return $this->cfg(self::P__PAGE_IN_STORES);}

	/** @return string */
	public function getPageTitle() {return $this->cfg(self::P__PAGE_TITLE);}

	/**
	 * Retrieve parent node's children.
	 * @return array
	 */
	public function getParentNodeChildren()
	{
		$children = $this->getResource()->getParentNodeChildren($this);
		/** @var Df_Cms_Model_Hierarchy_Node $blankModel */
		$blankModel = Df_Cms_Model_Hierarchy_Node::i();
		foreach ($children as $childId => $child) {
			/** @var Df_Cms_Model_Hierarchy_Node $newModel */
			$newModel = clone $blankModel;
			$newModel->setData($child);
			$children[$childId] = $newModel;
		}
		return $children;
	}

	/** @return int */
	public function getParentNodeId() {return $this->cfg(self::P__PARENT_NODE_ID);}

	/** @return string|null */
	public function getRequestUrl() {return $this->cfg(self::P__REQUEST_URL);}

	/**
	 * @override
	 * @return Df_Cms_Model_Resource_Hierarchy_Node_Collection
	 */
	public function getResourceCollection() {return self::c();}

	/** @return int */
	public function getSortOrder() {return $this->cfg(self::P__SORT_ORDER);}

	/**
	 * Get tree meta data flags for current node's tree.
	 * @return array|bool
	 */
	public function getTreeMetaData() {
		if (is_null($this->_treeMetaData)) {
			$this->_treeMetaData = $this->getResource()->getTreeMetaData($this);
		}
		return $this->_treeMetaData;
	}
	/** @@var array|bool  */
	private $_treeMetaData;

	/**
	 * Retrieve Tree Slice like two level array of node models.
	 *
	 * @param int $up, if equals zero - no limitation
	 * @param int $down, if equals zero - no limitation
	 * @return array
	 */
	public function getTreeSlice($up = 0, $down = 0)
	{
		$data =
			$this->getResource()
				->setTreeMaxDepth($this->_getData('tree_max_depth'))
				->setTreeIsBrief($this->_getData('tree_is_brief'))
				->getTreeSlice($this, $up, $down)
		;
		/** @var Df_Cms_Model_Hierarchy_Node $blankModel */
		$blankModel = Df_Cms_Model_Hierarchy_Node::i();
		foreach ($data as $parentId => $children) {
			foreach ($children as $childId => $child) {
				/** @var Df_Cms_Model_Hierarchy_Node $newModel */
				$newModel = clone $blankModel;
				$newModel->setData($child);
				$data[$parentId][$childId] = $newModel;
			}
		}
		return $data;
	}

	/** @return string|null */
	public function getXPath() {return $this->cfg(self::P__XPATH);}

	/**
	 * Retrieve Page URL
	 *
	 * @param mixed $store
	 * @return string
	 */
	public function getUrl($store = null) {
		/** @var array(string => string) $urlParams */
		$urlParams = array('_direct' => trim($this->getRequestUrl()));
		return
			!$store
			// для ускорения
			? Mage::getUrl('', $urlParams)
			: rm_store($store)->getUrl('', $urlParams);
	}

	/** @return bool */
	public function isExcludedFromMenu() {return $this->cfg(self::P__MENU_EXCLUDED);}

	/**
	 * @param int $nodeId
	 * @return bool
	 */
	public function isBelongTo($nodeId) {
		df_param_integer($nodeId, 0);
		if (!isset($this->{__METHOD__}[$nodeId])) {
			$this->{__METHOD__}[$nodeId] = in_array($nodeId, df_explode_xpath($this->getXPath()));
		}
		return $this->{__METHOD__}[$nodeId];
	}

	/**
	 * Is Node used original Page Identifier
	 * @return bool
	 */
	public function isUseDefaultIdentifier() {return is_null($this->_getData('identifier'));}

	/**
	 * Is Node used original Page Label
	 * @return bool
	 */
	public function isUseDefaultLabel(){return is_null($this->_getData('label'));}

	/**
	 * Load node by Request Url
	 *
	 * @param string $url
	 * @return Df_Cms_Model_Hierarchy_Node
	 */
	public function loadByRequestUrl($url)
	{
		$this->getResource()->loadByRequestUrl($this, $url);
		$this->_afterLoad();
		$this->setOrigData();
		return $this;
	}

	/**
	 * Retrieve first child node
	 *
	 * @param int $parentNodeId
	 * @return Df_Cms_Model_Hierarchy_Node
	 */
	public function loadFirstChildByParent($parentNodeId)
	{
		$this->getResource()->loadFirstChildByParent($this, $parentNodeId);
		$this->_afterLoad();
		$this->setOrigData();
		return $this;
	}

	/**
	 * Load page data for model if defined page id end undefined page data
	 * @return Df_Cms_Model_Hierarchy_Node
	 */
	public function loadPageData()
	{
		if ($this->getPageId() && !$this->getPageIdentifier()) {
			$this->getResource()->loadPageData($this);
		}
		return $this;
	}

	/**
	 * Flag to indicate whether append active pages only or not
	 *
	 * @param bool $flag
	 * @return Df_Cms_Model_Hierarchy_Node
	 */
	public function setCollectActivePagesOnly($flag) {
		$flag = !!$flag;
		$this->setData('collect_active_pages_only', $flag);
		$this->getResource()->setAppendActivePagesOnly($flag);
		return $this;
	}

	/**
	 * Flag to indicate whether append included pages (menu_excluded=0) only or not
	 *
	 * @param bool $flag
	 * @return Df_Cms_Model_Hierarchy_Node
	 */
	public function setCollectIncludedPagesOnly($flag) {
		$flag = !!$flag;
		$this->setData('collect_included_pages_only', $flag);
		$this->getResource()->setAppendIncludedPagesOnly($flag);
		return $this;
	}

	/**
	 * Setter for tree_max_depth data
	 * Maximum tree depth for tree slice, if equals zero - no limitations
	 *
	 * @param int $depth
	 * @return Df_Cms_Model_Hierarchy_Node
	 */
	public function setTreeMaxDepth($depth)
	{
		$this->setData('tree_max_depth', (int)$depth);
		return $this;
	}

	/**
	 * Setter for tree_is_brief data
	 * Tree Detalization, i.e. brief or detailed
	 *
	 * @param bool $brief
	 * @return Df_Cms_Model_Hierarchy_Node
	 */
	public function setTreeIsBrief($brief) {
		$this->setData('tree_is_brief', !!$brief);
		return $this;
	}

	/**
	 * Update rewrite for page (if identifier changed)
	 *
	 * @param Mage_Cms_Model_Page $page
	 * @return Df_Cms_Model_Hierarchy_Node
	 */
	public function updateRewriteUrls(Mage_Cms_Model_Page $page) {
		$xpaths = $this->getResource()->getTreeXpathsByPage($page->getId());
		foreach ($xpaths as $xpath) {
			$this->getResource()->updateRequestUrlsForTreeByXpath($xpath);
		}
		return $this;
	}

	/**
	 * Process additional data after save.
	 * @override
	 * @return Df_Cms_Model_Hierarchy_Node
	 */
	protected function _afterSave()
	{
		parent::_afterSave();
		// we save to metadata table not only metadata :(
		//if (Mage::helper('df_cms/hierarchy')->isMetadataEnabled()) {
			$this->getResource()->saveMetaData($this);
		//}
		return $this;
	}

	/**
	 * Recursive save nodes
	 *
	 * @param array $nodes
	 * @param int $parentNodeId
	 * @param string $path
	 * @param int $level
	 * @return Df_Cms_Model_Hierarchy_Node
	 */
	protected function _collectTree(array $nodes, $parentNodeId, $path = '', $xpath = '', $level = 0)
	{
		if (!isset($nodes[$level])) {
			return $this;
		}
		foreach ($nodes[$level] as $k => $v) {
			/**
			 * Свойство «parent_node_id» надо инициализировать
			 * только если оно не пусто и не равно 0,
			 * иначе в Magent CE 1.9 (а, может, и в других версиях)
			 * происходит сбой:
			 *
				Integrity constraint violation:
				1452 Cannot add or update a child row:
				a foreign key constraint fails
				(`14_05_21_sandbox_1901`.`df_cms_hierarchy_node`,
				CONSTRAINT `FK_DF_CMS_HIERARCHY_NODE_PARENT_NODE`
				FOREIGN KEY (`parent_node_id`) REFERENCES `df_cms_hierarchy_node` (`node_id`)
				ON DELETE CASCA)
			 *
			 * Другими словами,
			 * сохранять добавлять такую запись в таблицу df_cms_hierarchy_node ошибочно:
					(
						[parent_node_id] => 0
						[page_id] =>
						[identifier] => тест
						[label] => тест
						[level] => 1
						[sort_order] => 0
						[request_url] => тест
						[xpath] =>
					)
			* Для исправления из этой записи надо удалить нулевой ключ parent_node_id:
				(
					[page_id] =>
					[identifier] => тест
					[label] => тест
					[level] => 1
					[sort_order] => 0
					[request_url] => тест
					[xpath] =>
				)
			 */
			if ($parentNodeId) {
				$v['parent_node_id'] = $parentNodeId;
			}
			if ($path != '') {
				$v['request_url'] = $path . '/' . $v['request_url'];
			} else {
				$v['request_url'] = $v['request_url'];
			}

			if ($xpath != '') {
				$v['xpath'] = $xpath . '/';
			} else {
				$v['xpath'] = '';
			}

			$object = clone $this;
			$object->setData($v)->save();
			if (isset($nodes[$k])) {
				$this->_collectTree($nodes, $object->getId(), $object->getRequestUrl(), $object->getXpath(), $k);
			}
		}
		return $this;
	}

	/**
	 * @override
	 * @return Df_Cms_Model_Resource_Hierarchy_Node
	 */
	protected function _getResource() {return Df_Cms_Model_Resource_Hierarchy_Node::s();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__LEVEL, DF_V_INT)
			->_prop(self::P__MENU_EXCLUDED, DF_V_BOOL)
			->_prop(self::P__MENU_LEVELS_DOWN, DF_V_INT)
			->_prop(self::P__PAGE_IDENTIFIER, DF_V_STRING)
			/**
			 * Оказывается, при загрузке статей из БД P__PAGE_TITLE может быть равно NULL:
				 #1 Df_Core_Model->_validate('page_title', NULL)
				 #2 Df_Core_Model->setData('page_title', NULL)
				 #3 Varien_Object->addData(Array)
				 #4 Varien_Data_Collection_Db->load()
			 * http://magento-forum.ru/topic/4626/
			 */
			->_prop(self::P__PAGE_TITLE, DF_V_STRING)
			->_prop(self::P__PARENT_NODE_ID, DF_V_NAT0)
			->_prop(self::P__REQUEST_URL, DF_V_STRING)
		    ->_prop(self::P__SORT_ORDER, DF_V_INT)
			->_prop(self::P__XPATH, DF_V_STRING)
		;
	}
	/** @var array */
	 protected $_metaNodes = array();

	/**
	 * @used-by Df_Cms_Model_ContentsMenu_Applicator::_construct()
	 * @used-by Df_Cms_Model_Resource_Hierarchy_Node_Collection::_construct()
	 */
	const _C = __CLASS__;
	const META_NODE_TYPE_CHAPTER = 'chapter';
	const META_NODE_TYPE_SECTION = 'section';
	const META_NODE_TYPE_FIRST = 'start';
	const META_NODE_TYPE_NEXT = 'next';
	const META_NODE_TYPE_PREVIOUS = 'prev';
	const P__ADDITIONAL_SETTINGS = 'additional_settings';
	const P__ID = 'node_id';
	const P__LEVEL = 'level';
	const P__MENU_EXCLUDED = 'menu_excluded';
	const P__MENU_LEVELS_DOWN = 'menu_levels_down';
	const P__PAGE_IDENTIFIER = 'page_identifier';
	const P__PAGE_IN_STORES = 'page_in_stores';
	const P__PAGE_TITLE = 'page_title';
	const P__PARENT_NODE_ID = 'parent_node_id';
	const P__REQUEST_URL = 'request_url';
	const P__SORT_ORDER = 'sort_order';
	const P__XPATH = 'xpath';

	/** @return Df_Cms_Model_Resource_Hierarchy_Node_Collection */
	public static function c() {return new Df_Cms_Model_Resource_Hierarchy_Node_Collection;}
	/** @return string[] */
	public static function getMetadataKeys() {
		return array_merge(
			array(
				'pager_visibility'
				,'pager_frame'
				,'pager_jump'
				,'menu_brief'
				,'menu_excluded'
				,'menu_levels_down'
				,'menu_ordered'
				,'menu_list_type'
			)
			,self::getMetadataKeysAdditional()
		);
	}
	/** @return array */
	public static function getMetadataKeysAdditional() {
		return array_merge(
			self::getMetadataKeysForPageType(Df_Cms_Model_ContentsMenu_PageType::FRONT)
			,self::getMetadataKeysForPageType(Df_Cms_Model_ContentsMenu_PageType::CATALOG_PRODUCT_LIST)
			,self::getMetadataKeysForPageType(Df_Cms_Model_ContentsMenu_PageType::CATALOG_PRODUCT_VIEW)
			,self::getMetadataKeysForPageType(Df_Cms_Model_ContentsMenu_PageType::ACCOUNT)
			,self::getMetadataKeysForPageType(Df_Cms_Model_ContentsMenu_PageType::CMS_OWN)
			,self::getMetadataKeysForPageType(Df_Cms_Model_ContentsMenu_PageType::CMS_FOREIGN)
			,self::getMetadataKeysForPageType(Df_Cms_Model_ContentsMenu_PageType::OTHER)
		);
	}
	/**
	 * @param string $pageType
	 * @param string $keyName
	 * @return string
	 */
	public static function getMetadataKeyForPageType($pageType, $keyName) {
		return implode('__', array('contents_menu', $pageType, $keyName));
	}
	/**
	 * @param string $pageType
	 * @return array
	 */
	public static function getMetadataKeysForPageType($pageType) {
		return array(
			self::getMetadataKeyForPageType($pageType, 'enabled')
			,self::getMetadataKeyForPageType($pageType, 'position')
			,self::getMetadataKeyForPageType($pageType, 'vertical_ordering')
		);
	}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Cms_Model_Hierarchy_Node
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_Cms_Model_Hierarchy_Node
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
	/** @return Df_Cms_Model_Hierarchy_Node */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}