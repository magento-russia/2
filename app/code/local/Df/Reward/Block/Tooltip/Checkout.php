<?php
/**
 * Checkout Tooltip block to show checkout cart message for gaining reward points
 */
class Df_Reward_Block_Tooltip_Checkout extends Df_Reward_Block_Tooltip {
	/**
	 * Set quote to the reward action instance
	 *
	 * @param int|string $action
	 */
	public function initRewardType($action)
	{
		parent::initRewardType($action);
		if ($this->_actionInstance) {
			$this->_actionInstance->setQuote(rm_quote());
		}
	}
}