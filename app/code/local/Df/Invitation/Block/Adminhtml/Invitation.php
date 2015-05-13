<?php
class Df_Invitation_Block_Adminhtml_Invitation extends Mage_Adminhtml_Block_Widget_Grid_Container {
	/** @return string */
	public function getHeaderCssClass() {
		return 'icon-head head-invitation';
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_controller = 'adminhtml_invitation';
		$this->_blockGroup = 'df_invitation';
		$this->_headerText = df_h()->invitation()->__('Manage Invitations');
		$this->_addButtonLabel = df_h()->invitation()->__('Add Invitations');
	}

	/** @return Df_Invitation_Block_Adminhtml_Invitation */
	public static function i() {return df_block(__CLASS__);}
}