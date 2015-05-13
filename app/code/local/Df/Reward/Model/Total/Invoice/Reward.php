<?php
/**
 * Reward sales order invoice total model
 */
class Df_Reward_Model_Total_Invoice_Reward extends Mage_Sales_Model_Order_Invoice_Total_Abstract {
	/**
	 * Collect reward total for invoice
	 *
	 * @param Mage_Sales_Model_Order_Invoice $invoice
	 * @return Df_Reward_Model_Total_Invoice_Reward
	 */
	public function collect(Mage_Sales_Model_Order_Invoice $invoice)
	{
		$order = $invoice->getOrder();
		$rewardCurrecnyAmountLeft = $order->getRewardCurrencyAmount() - $order->getRewardCurrencyAmountInvoiced();
		$baseRewardCurrecnyAmountLeft = $order->getBaseRewardCurrencyAmount() - $order->getBaseRewardCurrencyAmountInvoiced();
		if ($order->getBaseRewardCurrencyAmount() && $baseRewardCurrecnyAmountLeft > 0) {
			if ($baseRewardCurrecnyAmountLeft < $invoice->getBaseGrandTotal()) {
				$invoice->setGrandTotal($invoice->getGrandTotal() - $rewardCurrecnyAmountLeft);
				$invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $baseRewardCurrecnyAmountLeft);
			} else {
				$rewardCurrecnyAmountLeft = $invoice->getGrandTotal();
				$baseRewardCurrecnyAmountLeft = $invoice->getBaseGrandTotal();
				$invoice->setGrandTotal(0);
				$invoice->setBaseGrandTotal(0);
			}
			$pointValue = $order->getRewardPointsBalance() / $order->getBaseRewardCurrencyAmount();
			$rewardPointsBalance = $baseRewardCurrecnyAmountLeft*ceil($pointValue);
			$rewardPointsBalanceLeft = $order->getRewardPointsBalance() - $order->getRewardPointsBalanceInvoiced();
			if ($rewardPointsBalance > $rewardPointsBalanceLeft) {
				$rewardPointsBalance = $rewardPointsBalanceLeft;
			}
			$invoice->setRewardPointsBalance($rewardPointsBalance);
			$invoice->setRewardCurrencyAmount($rewardCurrecnyAmountLeft);
			$invoice->setBaseRewardCurrencyAmount($baseRewardCurrecnyAmountLeft);
		}
		return $this;
	}
}