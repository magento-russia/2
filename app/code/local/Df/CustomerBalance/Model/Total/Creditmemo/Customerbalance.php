<?php
class Df_CustomerBalance_Model_Total_Creditmemo_Customerbalance extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract {
	/**
	 * Collect customer balance totals for credit memo
	 *
	 * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
	 * @return Df_CustomerBalance_Model_Total_Creditmemo_Customerbalance
	 */
	public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
	{
		$creditmemo->setBaseCustomerBalanceTotalRefunded(0);
		$creditmemo->setCustomerBalanceTotalRefunded(0);
		$creditmemo->setBaseCustomerBalanceReturnMax(0);
		$creditmemo->setCustomerBalanceReturnMax(0);
		if (!Df_CustomerBalance_Helper_Data::s()->isEnabled()) {
			return $this;
		}

		$order = $creditmemo->getOrder();
		if ($order->getBaseCustomerBalanceAmount() && $order->getBaseCustomerBalanceInvoiced() != 0) {
			$cbLeft = $order->getBaseCustomerBalanceInvoiced() - $order->getBaseCustomerBalanceRefunded();
			$used = 0;
			$baseUsed = 0;
			if ($cbLeft >= $creditmemo->getBaseGrandTotal()) {
				$baseUsed = $creditmemo->getBaseGrandTotal();
				$used = $creditmemo->getGrandTotal();
				$creditmemo->setBaseGrandTotal(0);
				$creditmemo->setGrandTotal(0);
				$creditmemo->setAllowZeroGrandTotal(true);
			} else {
				$baseUsed = $order->getBaseCustomerBalanceInvoiced() - $order->getBaseCustomerBalanceRefunded();
				$used = $order->getCustomerBalanceInvoiced() - $order->getCustomerBalanceRefunded();
				$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal()-$baseUsed);
				$creditmemo->setGrandTotal($creditmemo->getGrandTotal()-$used);
			}

			$creditmemo->setBaseCustomerBalanceAmount($baseUsed);
			$creditmemo->setCustomerBalanceAmount($used);
		}

		$creditmemo->setBaseCustomerBalanceReturnMax($creditmemo->getBaseCustomerBalanceReturnMax() + $creditmemo->getBaseGrandTotal());
		$creditmemo->setBaseCustomerBalanceReturnMax($creditmemo->getBaseCustomerBalanceReturnMax() + $creditmemo->getBaseCustomerBalanceAmount());
		$creditmemo->setCustomerBalanceReturnMax($creditmemo->getCustomerBalanceReturnMax() + $creditmemo->getCustomerBalanceAmount());
		return $this;
	}
}