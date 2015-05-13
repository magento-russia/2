<?php
class Df_Sales_Block_Admin_Widget_Grid_Column_Renderer_Products
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	/**
	 * @override
	 * @param Varien_Object $row
	 * @return string
	 */
	public function render(Varien_Object $row) {
		return Df_Sales_Block_Admin_Widget_Grid_Column_RendererDf_Products::i($this, $row)->toHtml();
	}

	const _CLASS = __CLASS__;
}