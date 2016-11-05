<?php
class Df_Cms_Block_Admin_Page_Edit_Tab_Hierarchy
	extends Df_Core_Block_Admin
	implements Mage_Adminhtml_Block_Widget_Tab_Interface {
	/**
	 * Array of nodes for tree
	 * @var array|null
	 */
	protected $_nodes = null;

	/**
	 * Retrieve current page instance
	 * @return Mage_Cms_Model_Page
	 */
	public function getPage() {
		return Mage::registry('cms_page');
	}

	/**
	 * Retrieve Hierarchy JSON string
	 * @return string
	 */
	public function getNodesJson() {
		return df_mage()->coreHelper()->jsonEncode($this->getNodes());
	}

	/**
	 * Prepare nodes data from DB  all from session if error occurred.
	 * @return array
	 */
	public function getNodes() {
		if (is_null($this->_nodes)) {
			$this->_nodes = [];
			$data = df_mage()->coreHelper()->jsonDecode($this->getPage()->getNodesData());
			$collection = Df_Cms_Model_Hierarchy_Node::c();
			$collection
				->joinCmsPage()
				->setOrderByLevel()
				->joinPageExistsNodeInfo($this->getPage())
			;
			if (is_array($data)) {
				foreach ($data as $v) {
					if (isset($v['page_exists'])) {
						$pageExists = !!$v['page_exists'];
					}
					else {
						$pageExists = false;
					}
					$node = array(
						'node_id' => $v['node_id']
						,'parent_node_id' => $v['parent_node_id']
						,'label' => $v['label']
						,'page_exists' => $pageExists
						,'page_id' => $v['page_id']
						,'current_page' => !!$v['current_page']
					);
					/** @var Df_Cms_Model_Hierarchy_Node $item */
					$item = $collection->getItemById($v['node_id']);
					$node['assigned_to_stores'] = !$item ? array() : $this->getPageStoreIds($item);
					$this->_nodes[]= $node;
				}
			} else {
				foreach ($collection as $item) {
					/** @var Df_Cms_Model_Hierarchy_Node $item */
					$this->_nodes[]= array(
						'node_id' => $item->getId()
						,'parent_node_id' => $item->getParentNodeId()
						,'label' => $item->getLabel()
						,'page_exists' => !!$item->getPageExists()
						,'page_id' => $item->getPageId()
						,'current_page' => !!$item->getCurrentPage()
						,'assigned_to_stores' => $this->getPageStoreIds($item)
					);
				}
			}
		}
		return $this->_nodes;
	}

	/**
	 * @param Df_Cms_Model_Hierarchy_Node $node
	 * @return int[]
	 */
	public function getPageStoreIds(Df_Cms_Model_Hierarchy_Node $node) {
		return
			(!$node->getPageId() || !$node->getPageInStores())
			? array()
			: df_csv_parse_int($node->getPageInStores())
		;
	}

	/**
	 * Forced nodes setter
	 *
	 * @param array $nodes New nodes array
	 * @return Df_Cms_Block_Admin_Page_Edit_Tab_Hierarchy
	 */
	public function setNodes($nodes) {
		if (is_array($nodes)) {
			$this->_nodes = $nodes;
		}
		return $this;
	}

	/**
	 * Retrieve ids of selected nodes from two sources.
	 * First is from prepared data from DB.
	 * Second source is data from page model in case we had error.
	 * @return string
	 */
	public function getSelectedNodeIds() {
		if (!$this->getPage()->hasData('node_ids')) {
			$ids = [];
			foreach ($this->getNodes() as $node) {
				if (isset($node['page_exists']) && $node['page_exists']) {
					$ids[]= $node['node_id'];
				}
			}
			return df_csv($ids);
		}
		return $this->getPage()->getData('node_ids');
	}

	/** @return string */
	public function getCurrentPageJson() {
		return df_mage()->coreHelper()->jsonEncode(
			array('label' => $this->getPage()->getTitle(),'id' => $this->getPage()->getId())
		);
	}
	/**
	 * @override
	 * @return string
	 */
	public function getTabLabel() {return df_h()->cms()->__('Hierarchy');}
	/**
	 * @override
	 * @return string
	 */
	public function getTabTitle() {return df_h()->cms()->__('Hierarchy');}
	/**
	 * Check is can show tab
	 * @return bool
	 */
	public function canShowTab() {
		/**
		 * Текущая программная реализация
		 * позволяет обрабатывать привязку только уже существующих в БД страниц
		 * (не только что созданных)
		 * к товарным раделам
		 */
		return
			$this->getPage()->getId()
			&& df_cfgr()->cms()->hierarchy()->isEnabled()
			&& df_admin_allowed('cms/hierarchy')
		;
	}

	/**
	 * @override
	 * @return bool
	 */
	public function isHidden() {return false;}
}