<?php
class Df_CustomerBalance_Model_Total_Invoice_Customerbalance extends Mage_Sales_Model_Order_Invoice_Total_Abstract {
	/**
	 * Collect customer balance totals for invoice
	 *
	 * @param Mage_Sales_Model_Order_Invoice $invoice
	 * @return Df_CustomerBalance_Model_Total_Invoice_Customerbalance
	 */
	public function collect(Mage_Sales_Model_Order_Invoice $invoice)
	{
		if (!df_h()->customer()->balance()->isEnabled()) {
			return $this;
		}
		$order = $invoice->getOrder();
		if ($order->getBaseCustomerBalanceAmount() && $order->getBaseCustomerBalanceInvoiced() != $order->getBaseCustomerBalanceAmount()) {
			$gcaLeft = $order->getBaseCustomerBalanceAmount() - $order->getBaseCustomerBalanceInvoiced();
			$used = 0;
			$baseUsed = 0;
			if ($gcaLeft >= $invoice->getBaseGrandTotal()) {
				$baseUsed = $invoice->getBaseGrandTotal();
				$used = $invoice->getGrandTotal();
				$invoice->setBaseGrandTotal(0);
				$invoice->setGrandTotal(0);
			} else {
				$baseUsed = $order->getBaseCustomerBalanceAmount() - $order->getBaseCustomerBalanceInvoiced();
				$used = $order->getCustomerBalanceAmount() - $order->getCustomerBalanceInvoiced();
				$invoice->setBaseGrandTotal($invoice->getBaseGrandTotal()-$baseUsed);
				$invoice->setGrandTotal($invoice->getGrandTotal()-$used);
			}

			$invoice->setBaseCustomerBalanceAmount($baseUsed);
			$invoice->setCustomerBalanceAmount($used);
		}
		return $this;
	}
}