<?php
class Df_Reward_Block_Customer_Reward_Subscription extends Df_Core_Block_Template_NoCache {
	/** @return bool */
	public function isSubscribedForUpdates() {
		return !!$this->_getCustomer()->getRewardUpdateNotification();
	}

	/** @return bool */
	public function isSubscribedForWarnings() {
		return !!$this->_getCustomer()->getRewardWarningNotification();
	}

	/** @return Df_Customer_Model_Customer */
	protected function _getCustomer() {return df_session_customer()->getCustomer();}
}