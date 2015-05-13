<?php
class Df_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Balance extends Df_Core_Block_Admin {
	/**
	 * @deprecated after 1.3.2.3
	 * @return int
	 */
	public function getOneBalanceTotal()
	{
		return 0;
	}

	/**
	 * @deprecated after 1.3.2.3
	 * @return bool
	 */
	public function shouldShowOneBalance()
	{
		return false;
	}

	/**
	 * Get delete orphan balances button
	 * @return string
	 */
	public function getDeleteOrphanBalancesButton() {
		$customer = Mage::registry('current_customer');
		if (
				0
			<
				Df_CustomerBalance_Model_Resource_Balance::s()
					->getOrphanBalancesCount(
						$customer->getId()
					)
		) {
			return
				df_block('adminhtml/widget_button')
					->setData(
						array(
							'label' => df_h()->customer()->balance()->__('Delete Orphan Balances')
							,'onclick' =>
								rm_sprintf(
									'setLocation(%s)'
									,df_quote_single($this->getDeleteOrphanBalancesUrl())
								)
							,'class' => 'scalable delete'
						)
					)->toHtml()
			;
		}
		return '';
	}

	/**
	 * Get delete orphan balances url
	 * @return string
	 */
	public function getDeleteOrphanBalancesUrl()
	{
		return $this->getUrl('*/customerbalance/deleteOrphanBalances', array('_current' => true, 'tab' => 'customer_info_tabs_customerbalance'));
	}
}