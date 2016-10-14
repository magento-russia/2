<?php
class Df_Invitation_Block_Adminhtml_Report_Invitation_General
	extends Mage_Adminhtml_Block_Widget_Grid_Container {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_controller = 'adminhtml_report_invitation_general';
		$this->_blockGroup = 'df_invitation';
		$this->_headerText = df_h()->invitation()->__('General');
		$this->_removeButton('add');
	}
}