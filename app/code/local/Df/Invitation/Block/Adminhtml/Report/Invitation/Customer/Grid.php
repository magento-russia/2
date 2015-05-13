<?php
class Df_Invitation_Block_Adminhtml_Report_Invitation_Customer_Grid
	extends Mage_Adminhtml_Block_Report_Grid {
	/**
	 * Prepare report collection
	 * @return Df_Invitation_Block_Adminhtml_Report_Invitation_Customer_Grid
	 */
	protected function _prepareCollection()
	{
		parent::_prepareCollection();
		$this->getCollection()->initReport('df_invitation/report_invitation_customer_collection');
		return $this;
	}

	/**
	 * Prepare report grid columns
	 * @return Df_Invitation_Block_Adminhtml_Report_Invitation_Customer_Grid
	 */
	protected function _prepareColumns()
	{
		$this->addColumn('id', array(
			'header'	=>df_h()->invitation()->__('ID'),'index'	 => 'entity_id'
		));
		$this->addColumn('name', array(
			'header'	=>df_h()->invitation()->__('Name'),'index'	 => 'name'
		));
		$this->addColumn('email', array(
			'header'	=>df_h()->invitation()->__('Email'),'index'	 => 'email'
		));
		$this->addColumn('group', array(
			'header'	=>df_h()->invitation()->__('Group'),'index'	 => 'group_name'
		));
		$this->addColumn('sent', array(
			'header'	=>df_h()->invitation()->__('Invitations Sent'),'type'	  =>'number','index'	 => 'sent'
		));
		$this->addColumn('accepted', array(
			'header'	=>df_h()->invitation()->__('Invitations Accepted'),'type'	  =>'number','index'	 => 'accepted'
		));
		$this->addExportType('*/*/exportCustomerCsv', df_h()->invitation()->__('CSV'));
		$this->addExportType('*/*/exportCustomerExcel', df_h()->invitation()->__('Excel'));
		return parent::_prepareColumns();
	}

	/** @return Df_Invitation_Block_Adminhtml_Report_Invitation_Customer_Grid */
	public static function i() {return df_block(__CLASS__);}
}