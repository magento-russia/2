<?php
/**
 * Reward sales order creditmemo total model
 */
class Df_Reward_Model_Total_Creditmemo_Reward extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract {
	/**
	 * Collect reward totals for credit memo
	 *
	 * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
	 * @return Df_Reward_Model_Total_Creditmemo_Reward
	 */
	public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
	{
		$order = $creditmemo->getOrder();
		$rewardCurrecnyAmountLeft = $order->getRewardCurrencyAmountInvoiced() - $order->getRewardCurrencyAmountRefunded();
		$baseRewardCurrecnyAmountLeft = $order->getBaseRewardCurrencyAmountInvoiced() - $order->getBaseRewardCurrencyAmountRefunded();
		if ($order->getBaseRewardCurrencyAmount() && $baseRewardCurrecnyAmountLeft > 0) {
			if ($baseRewardCurrecnyAmountLeft >= $creditmemo->getBaseGrandTotal()) {
				$rewardCurrecnyAmountLeft = $creditmemo->getGrandTotal();
				$baseRewardCurrecnyAmountLeft = $creditmemo->getBaseGrandTotal();
				$creditmemo->setGrandTotal(0);
				$creditmemo->setBaseGrandTotal(0);
				$creditmemo->setAllowZeroGrandTotal(true);
			} else {
				$creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $rewardCurrecnyAmountLeft);
				$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $baseRewardCurrecnyAmountLeft);
			}
			$pointValue = $order->getRewardPointsBalance() / $order->getBaseRewardCurrencyAmount();
			$rewardPointsBalance = $baseRewardCurrecnyAmountLeft*ceil($pointValue);
			$rewardPointsBalanceLeft = $order->getRewardPointsBalance() - $order->getRewardPointsBalanceRefunded();
			if ($rewardPointsBalance > $rewardPointsBalanceLeft) {
				$rewardPointsBalance = $rewardPointsBalanceLeft;
			}
			$creditmemo->setRewardPointsBalance($rewardPointsBalance);
			$creditmemo->setRewardCurrencyAmount($rewardCurrecnyAmountLeft);
			$creditmemo->setBaseRewardCurrencyAmount($baseRewardCurrecnyAmountLeft);
		}
		return $this;
	}
}