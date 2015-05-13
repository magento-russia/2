<?php
/**
 * Reward action to add points to inviter when his referral becomes customer
 */
class Df_Reward_Model_Action_InvitationCustomer extends Df_Reward_Model_Action_Abstract {
	/**
	 * Retrieve points delta for action
	 *
	 * @param int $websiteId
	 * @return int
	 */
	public function getPoints($websiteId)
	{
		return(int)df_h()->reward()->getPointsConfig('invitation_customer', $websiteId);
	}

	/**
	 * Check whether rewards can be added for action
	 * @return bool
	 */
	public function canAddRewardPoints()
	{
		/** @var bool $result */
		$result = false;
		if (df_module_enabled(Df_Core_Module::INVITATION)) {
			$invitation = $this->getEntity();
			if ($invitation->getData('status') != Df_Invitation_Model_Invitation::STATUS_ACCEPTED) {
				$result = false;
			}
			else {
				$result = !($this->isRewardLimitExceeded());
			}
		}
		return $result;
	}

	/**
	 * Return pre-configured limit of rewards for action
	 * @return int|string
	 */
	public function getRewardLimit()
	{
		return df_h()->reward()->getPointsConfig('invitation_customer_limit', $this->getReward()->getWebsiteId());
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
		return df_h()->reward()->__('Invitation to %s converted into a customer.', $email);
	}

	/**
	 * Setter for $_entity and add some extra data to history
	 *
	 * @param Varien_Object $entity
	 * @return Df_Reward_Model_Action_Abstract
	 */
	public function setEntity($entity)
	{
		parent::setEntity($entity);
		$this->getHistory()->addAdditionalData(array(
			'email' => $this->getEntity()->getEmail()
		));
		return $this;
	}
}