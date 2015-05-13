<?php
/**
 * Cообщение:		«rm_adminhtml_block_sales_order_grid__prepare_columns_after»
 * Источник:		Df_Adminhtml_Block_Sales_Order_Grid::_prepareColumns()
 * [code]
		Mage::dispatchEvent(
			Df_Core_Model_Event_Adminhtml_Block_Sales_Order_Grid_PrepareColumnsAfter::EVENT
			,array('grid' => $this)
		);
 * [/code]
 */
class Df_Core_Model_Event_Adminhtml_Block_Sales_Order_Grid_PrepareColumnsAfter
	extends Df_Core_Model_Event {
	/** @return Df_Adminhtml_Block_Sales_Order_Grid */
	public function getGrid() {return $this->getEventParam(self::EVENT_PARAM__GRID);}
	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedEventPrefix() {return self::EVENT;}

	const _CLASS = __CLASS__;
	const EVENT = 'rm_adminhtml_block_sales_order_grid__prepare_columns_after';
	const EVENT_PARAM__GRID = 'grid';
}