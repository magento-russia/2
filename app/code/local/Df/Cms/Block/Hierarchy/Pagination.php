<?php
class Df_Cms_Block_Hierarchy_Pagination extends Df_Core_Block_Template {
	/**
	 * Current Hierarchy Node Page Instance
	 *
	 * @var Df_Cms_Model_Hierarchy_Node
	 */
	protected $_node;

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		if ($this->getNodeId()) {
			$this->_node =
				Df_Cms_Model_Hierarchy_Node::ld(
					$this->getNodeId()
				)
			;
		} else {
			$this->_node = Mage::registry('current_cms_hierarchy_node');
		}

		$this->setData('sequence', 1);
		$this->setData('outer', 1);
		$this->setData('frame', 10);
		$this->setData('jump', 0);
		$this->setData('use_node_labels', 0);
		$this->_loadNodePaginationParams();
		/**
		 * Чтобы блок кэшировался стандартным, заложенным в @see Mage_Core_Block_Abstract способом,
		 * продолжительность хранения кэша надо указывать обязательно,
		 * потому что значением продолжительности по умолчанию является «null»,
		 * что в контексте @see Mage_Core_Block_Abstract
		 * (и в полную противоположность Zend Framework
		 * и всем остальным частям Magento, где используется кэширование)
		 * означает, что блок не удет кэшироваться вовсе!
		 * @used-by Mage_Core_Block_Abstract::_loadCache()
		 */
		$this->setData('cache_lifetime', self::CACHE_LIFETIME_STANDARD);
	}

	/**
	 * Add context menu params to block data
	 * @return void
	 */
	protected function _loadNodePaginationParams() {
		$this->setPaginationEnabled(false);
		if ($this->_node instanceof Mage_Core_Model_Abstract) {
			$params = $this->_node->getMetadataPagerParams();
			if (
					!is_null($params)
				&&
					isset($params['pager_visibility'])
				&&
					(
							Df_Cms_Helper_Hierarchy::METADATA_VISIBILITY_YES
						===
							$params['pager_visibility']
					)
			) {
				$this->addData(array(
					'jump' => isset($params['pager_jump']) ? $params['pager_jump'] : 0,'frame' => isset($params['pager_frame']) ? $params['pager_frame'] : 0,));
				$this->setPaginationEnabled(true);
			}
		}
	}

	/**
	 * Use Node label instead of numeric pages
	 * @return bool
	 */
	public function getUseNodeLabels()
	{
		return $this->_getData('use_node_labels') > 0;
	}

	/**
	 * Can show Previous and Next links
	 * @return bool
	 */
	public function canShowSequence()
	{
		return $this->_getData('sequence') > 0;
	}

	/**
	 * Can show First and Last links
	 * @return bool
	 */
	public function canShowOuter()
	{
		return $this->getJump() > 0 && $this->_getData('outer') > 0;
	}

	/**
	 * Retrieve how many links to pages to show as one frame in the pagination widget.
	 * @return int
	 */
	public function getFrame() {return abs(df_int($this->_getData('frame')));}

	/**
	 * Retrieve whether to show link to page number current + y
	 * that extends frame size if applicable
	 * @return int
	 */
	public function getJump() {return abs(($this->_getData('jump')));}

	/**
	 * Retrieve node label or number
	 *
	 * @param Df_Cms_Model_Hierarchy_Node $node
	 * @param string $custom instead of page number
	 * @return string
	 */
	public function getNodeLabel(Df_Cms_Model_Hierarchy_Node $node, $custom = null)
	{
		if ($this->getUseNodeLabels()) {
			return $node->getLabel();
		}
		if (!is_null($custom)) {
			return $custom;
		}
		return $node->getPageNumber();
	}

	/**
	 * Can show First page
	 * @return bool
	 */
	public function canShowFirst()
	{
		return $this->getCanShowFirst();
	}

	/**
	 * Retrieve First node page
	 * @return Df_Cms_Model_Hierarchy_Node
	 */
	public function getFirstNode()
	{
		return $this->_getData('first_node');
	}

	/**
	 * Can show Last page
	 * @return bool
	 */
	public function canShowLast()
	{
		return $this->getCanShowLast();
	}

	/**
	 * Retrieve First node page
	 * @return Df_Cms_Model_Hierarchy_Node
	 */
	public function getLastNode()
	{
		return $this->_getData('last_node');
	}

/**
	 * Can show Previous  page link
	 * @return bool
	 */
	public function canShowPrevious()
	{
		return $this->getPreviousNode() !== null;
	}

	/**
	 * Retrieve Previous  node page
	 * @return Df_Cms_Model_Hierarchy_Node
	 */
	public function getPreviousNode()
	{
		return $this->_getData('previous_node');
	}

	/**
	 * Can show Next page link
	 * @return bool
	 */
	public function canShowNext()
	{
		return $this->getNextNode() !== null;
	}

	/**
	 * Retrieve Next node page
	 * @return Df_Cms_Model_Hierarchy_Node
	 */
	public function getNextNode()
	{
		return $this->_getData('next_node');
	}

	/**
	 * Can show Previous Jump page link
	 * @return bool
	 */
	public function canShowPreviousJump()
	{
		return $this->getJump() > 0 && $this->getCanShowPreviousJump();
	}

	/**
	 * Retrieve Previous Jump node page
	 * @return Df_Cms_Model_Hierarchy_Node
	 */
	public function getPreviousJumpNode()
	{
		return $this->_getData('previous_jump');
	}

	/**
	 * Can show Next Jump page link
	 * @return bool
	 */
	public function canShowNextJump()
	{
		return $this->getJump() > 0 && $this->getCanShowNextJump();
	}

	/**
	 * Retrieve Next Jump node page
	 * @return Df_Cms_Model_Hierarchy_Node
	 */
	public function getNextJumpNode()
	{
		return $this->_getData('next_jump');
	}

	/**
	 * Is Show Previous and Next links
	 * @return bool
	 */
	public function isShowOutermost()
	{
		return $this->_getData('outermost') > 1;
	}

	/**
	 * Retrieve Nodes collection array
	 * @return array(int => Df_Cms_Model_Hierarchy_Node)
	 */
	public function getNodes() {
		if (!$this->hasData('_nodes')) {
			/** @var array(int => Df_Cms_Model_Hierarchy_Node) $nodes */
			$nodes	=
				$this->_node
					->setCollectActivePagesOnly(true)
					->getParentNodeChildren()
			;
			$flags = array('previous' => false, 'next' => false);
			$count = count($nodes);
			$previous = null;
			$next = null;
			$first = null;
			$last = null;
			$current = 0;
			foreach ($nodes as $k => $node) {
				/** @var Df_Cms_Model_Hierarchy_Node $node */
				$node->setPageNumber($k + 1);
				$node->setIsCurrent(false);
				if (is_null($first)) {
					$first = $node;
				}
				if ($flags['next']) {
					$next = $node;
					$flags['next'] = false;
				}
				if ($node->getId() === $this->_node->getId()) {
					$flags['next'] = true;
					$flags['previous'] = true;
					$current = $k;
					$node->setIsCurrent(true);
				}
				if (!$flags['previous']) {
					$previous = $node;
				}
				$last = $node;
			}

			$this->setPreviousNode($previous);
			$this->setFirstNode($first);
			$this->setLastNode($last);
			$this->setNextNode($next);
			$this->setCanShowNext($next !== null);
			// calculate pages frame range
			if ($this->getFrame() > 0) {
				$middleFrame = ceil($this->getFrame() / 2);
				if ($count > $this->getFrame() && $current < $middleFrame) {
					$start = 0;
				} else {
					$start = $current - $middleFrame + 1;
					if (($start + 1 + $this->getFrame()) > $count) {
						$start = $count - $this->getFrame();
					}
				}
				if ($start > 0) {
					$this->setCanShowFirst(true);
				} else {
					$this->setCanShowFirst(false);
				}
				$end = $start + $this->getFrame();
				if ($end < $count) {
					$this->setCanShowLast(true);
				} else {
					$this->setCanShowLast(false);
				}
			} else {
				$this->setCanShowFirst(false);
				$this->setCanShowLast(false);
				$start = 0;
				$end   = $count;
			}

			$this->setCanShowPreviousJump(false);
			$this->setCanShowNextJump(false);
			if ($start > 1) {
				$this->setCanShowPreviousJump(true);
				if ($start - 1 > $this->getJump() * 2) {
					$jump = $start - $this->getJump();
				} else {
					$jump = ceil(($start - 1) / 2);
				}
				$this->setPreviousJump($nodes[$jump]);
			}
			if ($count - 1 > $end) {
				$this->setCanShowNextJump(true);
				$difference = $count - $end - 1;
				if ($difference < ($this->getJump() * 2)) {
					$jump = $end + ceil($difference / 2) - 1;
				} else {
					$jump = $end + $this->getJump() - 1;
				}
				$this->setNextJump($nodes[$jump]);
			}

			$this->setRangeStart($start);
			$this->setRangeEnd($end);
			$this->setData('_nodes', $nodes);
		}
		return $this->_getData('_nodes');
	}

	/**
	 * Retrieve nodes in range
	 * @return array
	 */
	public function getNodesInRange()
	{
		$range = [];
		$nodes = $this->getNodes();
		foreach ($nodes as $k => $node) {
			if ($k >= $this->getRangeStart() && $k < $this->getRangeEnd()) {
				$range[]= $node;
			}
		}
		return $range;
	}

	/**
	 * @override
	 * @see Mage_Core_Block_Template::_toHtml()
	 * @used-by Mage_Core_Block_Abstract::toHtml()
	 * @return string
	 */
	protected function _toHtml() {
		if (!$this->_node || !$this->getPaginationEnabled()) {
			return '';
		}
		// collect nodes to output pagination in template
		$nodes = $this->getNodes();
		// don't display pagination with one page
		if (count($nodes) <= 1) {
			return '';
		}
		return parent::_toHtml();
	}

	/**
	 * @override
	 * @see Df_Core_Block_Template::cacheKeySuffix()
	 * @used-by Df_Core_Block_Template::getCacheKeyInfo()
	 * @return string|string[]
	 */
	protected function cacheKeySuffix() {return $this->_getData('node_id');}
}