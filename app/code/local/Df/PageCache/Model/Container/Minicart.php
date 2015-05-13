<?php
class Df_PageCache_Model_Container_Minicart extends Df_PageCache_Model_Container_Sidebar_Cart
{
	/**
	 * Render block content
	 *
	 * @return string
	 */
	protected function _renderBlock()
	{
		$layout = $this->_getLayout('default');
		$block = $layout->getBlock('minicart_head');
		$block->setSkipRenderTag(true);
		return $block->toHtml();
	}
}
