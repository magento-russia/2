<?php
class Df_Reward_Block_Customer_Reward_Subscription extends Df_Core_Block_Template_NoCache {
	/**
	 * Getter for RewardUpdateNotification
	 * @return bool
	 */
	public function isSubscribedForUpdates() {
		return !!$this->_getCustomer()->getRewardUpdateNotification();
	}

	/**
	 * Getter for RewardWarningNotification
	 * @return bool
	 */
	public function isSubscribedForWarnings() {
		return !!$this->_getCustomer()->getRewardWarningNotification();
	}

	/**
	 * Retrieve customer model
	 * @return Mage_Customer_Model_Customer
	 */
	protected function _getCustomer()
	{
		return rm_session_customer()->getCustomer();
	}
}