<?php
/**
 * Reward action for new customer registration
 */
class Df_Reward_Model_Action_Register extends Df_Reward_Model_Action_Abstract {
	/**
	 * Retrieve points delta for action
	 *
	 * @param int $websiteId
	 * @return int
	 */
	public function getPoints($websiteId)
	{
		return(int)df_h()->reward()->getPointsConfig('register', $websiteId);
	}

	/**
	 * Return action message for history log
	 *
	 * @param array $args Additional history data
	 * @return string
	 */
	public function getHistoryMessage($args = array())
	{
		return df_h()->reward()->__('Registered as customer.');
	}
}