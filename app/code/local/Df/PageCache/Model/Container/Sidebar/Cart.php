<?php
class Df_PageCache_Model_Container_Sidebar_Cart extends Df_PageCache_Model_Container_Advanced_Quote {
	/**
	 * Render block content
	 *
	 * @return string
	 */
	protected function _renderBlock()
	{
		/** @var Mage_Checkout_Block_Cart_Sidebar $block */
		$block = $this->_getPlaceHolderBlock();
		$block->setChild('extra_actions', $this->_getExtraActionsChildBlock());
		$renders = $this->_placeholder->getAttribute('item_renders');
		$block->deserializeRenders($renders);
		Mage::dispatchEvent('render_block', array('block' => $block, 'placeholder' => $this->_placeholder));
		return $block->toHtml();
	}

	/**
	 * Get child Block
	 *
	 * @return Mage_Core_Block_Abstract
	 */
	protected function _getExtraActionsChildBlock()
	{
		return $this->_getLayout()->getBlock('topCart.extra_actions');
	}
}
