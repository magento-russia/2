<?php
/**
 * Reward action for updating balance by administrator
 */
class Df_Reward_Model_Action_Admin extends Df_Reward_Model_Action_Abstract {
	/**
	 * Check whether rewards can be added for action
	 * @return bool
	 */
	public function canAddRewardPoints()
	{
		return true;
	}

	/**
	 * Return action message for history log
	 *
	 * @param array $args Additional history data
	 * @return string
	 */
	public function getHistoryMessage($args = array())
	{
		return df_h()->reward()->__('Updated by moderator.');
	}
}