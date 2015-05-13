<?php
class Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management extends Df_Core_Block_Admin {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->setTemplate('df/reward/customer/edit/management.phtml');
	}

	/**
	 * Prepare layout
	 * @return Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management
	 */
	protected function _prepareLayout(){
		$this->setChild(
			'balance', Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Balance::i()
		);
		$this->setChild(
			'update', Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Update::i()
		);
		return parent::_prepareLayout();
	}
}