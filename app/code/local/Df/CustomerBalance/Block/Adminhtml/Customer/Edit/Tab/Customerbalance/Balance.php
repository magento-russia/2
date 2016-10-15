<?php
class Df_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Balance extends Df_Core_Block_Admin {
	/**
	 * Get delete orphan balances url
	 * @return string
	 */
	public function getDeleteOrphanBalancesUrl() {
		return $this->getUrl('*/customerbalance/deleteOrphanBalances', array(
			'_current' => true, 'tab' => 'customer_info_tabs_customerbalance'
		));
	}

	/**
	 * @deprecated after 1.3.2.3
	 * @return int
	 */
	public function getOneBalanceTotal() {return 0;}

	/**
	 * @deprecated after 1.3.2.3
	 * @return bool
	 */
	public function shouldShowOneBalance() {return false;}

	/**
	 * @used-by app/design/adminhtml/rm/default/template/df/customerbalance/balance.phtml
	 * @return string
	 */
	protected function getDeleteOrphanBalancesButton() {
		return
			!Df_CustomerBalance_Model_Resource_Balance::s()->getOrphanBalancesCount(
				Mage::registry('current_customer')->getId()
			)
			? ''
			: df_admin_button(array(
				'label' => Df_CustomerBalance_Helper_Data::s()->__('Delete Orphan Balances')
				,'onclick' => df_admin_button_location($this->getDeleteOrphanBalancesUrl())
				,'class' => 'scalable delete'
			))
		;
	}
}