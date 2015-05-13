<?php
class Df_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Balance_History
	extends Df_Core_Block_Admin {
	/**
	 * @override
	 * @return Df_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Balance_History
	 */
	protected function _prepareLayout() {
		$this->setChild(
			'grid'
			, Df_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Balance_History_Grid
				::i('customer.balance.history.grid')
		);
		parent::_prepareLayout();
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->setTemplate('df/customerbalance/balance/history.phtml');
	}
}