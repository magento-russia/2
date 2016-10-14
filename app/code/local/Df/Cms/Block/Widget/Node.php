<?php
class Df_Cms_Block_Widget_Node
	extends Mage_Core_Block_Html_Link
	implements Mage_Widget_Block_Interface {
	/**
	 * Current Hierarchy Node Page Instance
	 *
	 * @var Df_Cms_Model_Hierarchy_Node
	 */
	protected $_node;

	/**
	 * Retrieve specified anchor text
	 * @return string
	 */
	public function getAnchorText()
	{
		if ($this->_getData('anchor_text')) {
			return $this->_getData('anchor_text');
		}
		return $this->_node->getLabel();
	}

	/**
	 * Retrieve link specified title
	 * @return string
	 */
	public function getTitle()
	{
		if ($this->_getData('title')) {
			return $this->_getData('title');
		}
		return $this->_node->getLabel();
	}

	/**
	 * Retrieve Node URL
	 * @return string
	 */
	public function getHref()
	{
		return $this->_node->getUrl();
	}

	/**
	 * @override
	 * @see Mage_Core_Block_Template::_toHtml()
	 * @used-by Mage_Core_Block_Abstract::toHtml()
	 * @return string
	 */
	protected function _toHtml()
	{
		if ($this->getNodeId()) {
			$this->_node =
				Df_Cms_Model_Hierarchy_Node::ld(
					$this->getNodeId()
				)
			;
		} else {
			$this->_node = Mage::registry('current_cms_hierarchy_node');
		}

		if (!$this->_node) {
			return '';
		}
		return parent::_toHtml();
	}
}