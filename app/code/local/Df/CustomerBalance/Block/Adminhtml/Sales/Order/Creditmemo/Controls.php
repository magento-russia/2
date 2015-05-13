<?php
class Df_CustomerBalance_Block_Adminhtml_Sales_Order_Creditmemo_Controls
	extends Df_Core_Block_Template {
	/**
	 * Check whether refund to customerbalance is available
	 * @return bool
	 */
	public function canRefundToCustomerBalance()
	{
		if (Mage::registry('current_creditmemo')->getOrder()->getCustomerIsGuest()) {
			return false;
		}
		return true;
	}

	/**
	 * Check whether real amount can be refunded to customer balance
	 * @return bool
	 */
	public function canRefundMoneyToCustomerBalance()
	{
		if (!Mage::registry('current_creditmemo')->getGrandTotal()) {
			return false;
		}

		if (Mage::registry('current_creditmemo')->getOrder()->getCustomerIsGuest()) {
			return false;
		}
		return true;
	}

	/**
	 * Prepopulate amount to be refunded to customerbalance
	 * @return float
	 */
	public function getReturnValue()
	{
		$max = Mage::registry('current_creditmemo')->getCustomerBalanceReturnMax();
		if ($max) {
			return $max;
		}
		return 0;
	}
}