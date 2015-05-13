<?php
/**
 * Customer Account empty block (using only just for adding RP link to tab)
 */
class Df_Reward_Block_Customer_Account extends Df_Core_Block_Abstract_NoCache {
	/**
	 * Add RP link to tab if we have all rates
	 * @return Df_Reward_Block_Customer_Account
	 */
	protected function _prepareLayout()
	{
		parent::_prepareLayout();
		$navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
		if (
				$navigationBlock
			&&
				df_h()->reward()->isEnabledOnFront()
			&&
				df_h()->reward()->getHasRates()
		) {
			$navigationBlock->addLink('df_reward', 'df_reward/customer/info/',df_h()->reward()->__('Reward Points'));
		}
		return $this;
	}
}