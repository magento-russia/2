<?php
class Df_Sales_Block_Admin_Grid_OrderItemsWrapper
	extends Df_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	/**
	 * @override
	 * @param Varien_Object $row
	 * @return string
	 */
	public function render(Varien_Object $row) {
		return Df_Sales_Block_Admin_Grid_OrderItems::r($this, $row);
	}

	/** @used-by Df_Sales_Model_Handler_AdminOrderGrid_AddProductColumn::registerProductColumnRenderer() */
	const _C = __CLASS__;
}