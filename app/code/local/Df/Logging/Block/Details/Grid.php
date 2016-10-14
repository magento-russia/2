<?php
/**
 * Admin Actions Log Archive grid
 *
 */
class Df_Logging_Block_Details_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->setId('loggingDetailsGrid');
		$this->setPagerVisibility(false);
		$this->setFilterVisibility(false);
	}

	/**
	 * Prepare grid collection
	 * @return Df_Logging_Block_Events_Archive_Grid
	 */
	protected function _prepareCollection()
	{
		$event = Mage::registry('current_event');
		$collection = Mage::getResourceModel('df_logging/event_changes_collection')
			->addFieldToFilter('event_id', $event->getId());
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	/**
	 * Prepare grid columns
	 * @return Df_Logging_Block_Events_Archive_Grid
	 */
	protected function _prepareColumns()
	{
		$this->addColumn('source_name', array(
			'header'	=> Df_Logging_Helper_Data::s()->__('Source Data'),'sortable'  => false,'renderer'  => 'df_logging/adminhtml_details_renderer_sourcename','index'	 => 'source_name','width'	 => 1
		));
		$this->addColumn('original_data', array(
			'header'	=> Df_Logging_Helper_Data::s()->__('Value Before Change'),'sortable'  => false,'renderer'  => 'df_logging/adminhtml_details_renderer_diff','index'	 => 'original_data'
		));
		$this->addColumn('result_data', array(
			'header'	=> Df_Logging_Helper_Data::s()->__('Value After Change'),'sortable'  => false,'renderer'  => 'df_logging/adminhtml_details_renderer_diff','index'	 => 'result_data'
		));
		return parent::_prepareColumns();
	}
}