<?php
class Df_Reward_Block_Checkout_Total extends Mage_Checkout_Block_Total_Default {
	protected $_template = 'df/reward/checkout/total.phtml';

	/**
	 * Return url to remove reward points from totals calculation
	 * @return string
	 */
	public function getRemoveRewardTotalUrl()
	{
		return $this->getUrl('df_reward/cart/remove');
	}
}