<?php
/**
 * Reward action to add points to inviter when his referral purchases order
 */
class Df_Reward_Model_Action_InvitationOrder extends Df_Reward_Model_Action_Abstract {
	/**
	 * Retrieve points delta for action
	 *
	 * @param int $websiteId
	 * @return int
	 */
	public function getPoints($websiteId)
	{
		return(int)df_h()->reward()->getPointsConfig('invitation_order', $websiteId);
	}

	/**
	 * Check whether rewards can be added for action
	 * @return bool
	 */
	public function canAddRewardPoints()
	{
		return !($this->isRewardLimitExceeded());
	}

	/**
	 * Return pre-configured limit of rewards for action
	 * @return int|string
	 */
	public function getRewardLimit()
	{
		return df_h()->reward()->getPointsConfig('invitation_order_limit', $this->getReward()->getWebsiteId());
	}

	/**
	 * Return action message for history log
	 *
	 * @param array $args Additional history data
	 * @return string
	 */
	public function getHistoryMessage($args = array())
	{
		$email = isset($args['email']) ? $args['email'] : '';
		return df_h()->reward()->__('Invitation to %s converted into an order.', $email);
	}

	/**
	 * Setter for $_entity and add some extra data to history
	 *
	 * @param Df_Invitation_Model_Invitation $entity
	 * @return Df_Reward_Model_Action_Abstract
	 */
	public function setEntity($entity)
	{
		parent::setEntity($entity);
		$this->getHistory()->addAdditionalData(array('email' => $this->getEntity()->getEmail()));
		return $this;
	}
}