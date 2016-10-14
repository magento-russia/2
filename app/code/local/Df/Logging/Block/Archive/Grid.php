<?php
/**
 * Admin Actions Log Archive grid
 *
 */
class Df_Logging_Block_Archive_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->setId('loggingArchiveGrid');
		$this->setDefaultSort('basename');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}

	/**
	 * Prepare grid collection
	 * @return Df_Logging_Block_Events_Archive_Grid
	 */
	protected function _prepareCollection() {
		$this->setCollection(Df_Logging_Model_Archive_Collection::s());
		return parent::_prepareCollection();
	}

	/**
	 * Prepare grid columns
	 * @return Df_Logging_Block_Events_Archive_Grid
	 */
	protected function _prepareColumns()
	{
		$downloadUrl = $this->getUrl('*/*/download');
		$this->addColumn('download', array(
			'header'	=> Df_Logging_Helper_Data::s()->__('Archive File'),'format'	=> '<a href="' . $downloadUrl .'basename/$basename/">$basename</a>','index'	 => 'basename',));
		$this->addColumn('date', array(
			'header'	=> Df_Logging_Helper_Data::s()->__('Date'),'type'	  => 'date','index'	 => 'time','filter'	=> 'df_logging/adminhtml_archive_grid_filter_date'
		));
		return parent::_prepareColumns();
	}

	/**
	 * Row click callback URL
	 * @return string
	 */
	public function getGridUrl()
	{
		return $this->getUrl('*/*/archiveGrid', array('_current' => true));
	}
}