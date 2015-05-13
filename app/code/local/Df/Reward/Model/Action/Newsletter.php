<?php
/**
 * Reward action for Newsletter Subscription
 */
class Df_Reward_Model_Action_Newsletter extends Df_Reward_Model_Action_Abstract {
	/**
	 * Retrieve points delta for action
	 *
	 * @param int $websiteId
	 * @return int
	 */
	public function getPoints($websiteId)
	{
		return(int)df_h()->reward()->getPointsConfig('newsletter', $websiteId);
	}

	/**
	 * Check whether rewards can be added for action
	 * @return bool
	 */
	public function canAddRewardPoints()
	{
		$subscriber = $this->getEntity();
		if ($subscriber->getData('subscriber_status') != Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED) {
			return false;
		}

		/* @var $subscribers Mage_Newsletter_Model_Mysql4_Subscriber_Collection */
		$subscribers = Mage::getResourceModel('newsletter/subscriber_collection')
			->addFieldToFilter('customer_id', $subscriber->getCustomerId())
			->load();
		// check for existing customer subscribtions
		$found = false;
		foreach ($subscribers as $item) {
			if ($subscriber->getSubscriberId() != $item->getSubscriberId()) {
				$found = true;
				break;
			}
		}
		return !$found;
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
		return df_h()->reward()->__('Signed up for newsletter with email %s.', $email);
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