<?php
class Df_Invitation_Block_Adminhtml_Report_Invitation_Customer
	extends Mage_Adminhtml_Block_Widget_Grid_Container {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_controller = 'adminhtml_report_invitation_customer';
		$this->_blockGroup = 'df_invitation';
		$this->_headerText = df_h()->invitation()->__('Customers');
		$this->_removeButton('add');
	}

	/** @return Df_Invitation_Block_Adminhtml_Report_Invitation_Customer */
	public static function i() {return df_block(__CLASS__);}
}