<?php
class Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Balance extends Df_Core_Block_Admin {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->setTemplate('df/reward/customer/edit/management/balance.phtml');
	}

	/**
	 * Prepare layout.
	 * Create balance grid block
	 * @return Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Balance
	 */
	protected function _prepareLayout()
	{
		if (!rm_admin_allowed('df_reward/balance')) {
			// unset template to get empty output
			$this->setTemplate(null);
		}
		else {
			$this->setChild('grid',
				df_block_l(new Df_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_Management_Balance_Grid)
			);
		}
		return parent::_prepareLayout();
	}
}