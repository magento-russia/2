<?php
class Df_Invitation_Block_Adminhtml_Report_Invitation_General_Grid
	extends Mage_Adminhtml_Block_Report_Grid {
	/**
	 * Prepare report collection
	 * @return Df_Invitation_Block_Adminhtml_Report_Invitation_General_Grid
	 */
	protected function _prepareCollection()
	{
		parent::_prepareCollection();
		$this->getCollection()->initReport('df_invitation/report_invitation_collection');
		return $this;
	}

	/**
	 * Prepare report grid columns
	 * @return Df_Invitation_Block_Adminhtml_Report_Invitation_General_Grid
	 */
	protected function _prepareColumns()
	{
		$this->addColumn('sent', array(
			'header'	=>df_h()->invitation()->__('Sent'),'type'	  =>'number','index'	 => 'sent'
		));
		$this->addColumn('accepted', array(
			'header'	=>df_h()->invitation()->__('Accepted'),'type'	  =>'number','index'	 => 'accepted','width'	 => ''
		));
		$this->addColumn('canceled', array(
			'header'	=> df_h()->invitation()->__('Discarded'),'type'	  =>'number','index'	 => 'canceled','width'	 => ''
		));
		$this->addColumn('accepted_rate', array(
			'header'	=>df_h()->invitation()->__('Acceptance Rate'),'index'	 =>'accepted_rate','renderer'  => 'df_invitation/adminhtml_grid_column_renderer_percent','type'	  =>'string','width'	 => '170'

		));
		$this->addColumn('canceled_rate', array(
			'header'	=>df_h()->invitation()->__('Discard Rate'),'index'	 =>'canceled_rate','type'	  =>'number','renderer'  => 'df_invitation/adminhtml_grid_column_renderer_percent','width'	 => '170'
		));
		$this->addExportType('*/*/exportCsv', df_h()->invitation()->__('CSV'));
		$this->addExportType('*/*/exportExcel', df_h()->invitation()->__('Excel'));
		return parent::_prepareColumns();
	}

	/** @return Df_Invitation_Block_Adminhtml_Report_Invitation_General_Grid */
	public static function i() {return df_block(__CLASS__);}
}