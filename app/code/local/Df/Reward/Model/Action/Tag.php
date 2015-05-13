<?php
/**
 * Reward action for tag submission
 */
class Df_Reward_Model_Action_Tag extends Df_Reward_Model_Action_Abstract {
	/**
	 * Retrieve points delta for action
	 *
	 * @param int $websiteId
	 * @return int
	 */
	public function getPoints($websiteId)
	{
		return(int)df_h()->reward()->getPointsConfig('tag', $websiteId);
	}

	/**
	 * Return pre-configured limit of rewards for action
	 * @return int|string
	 */
	public function getRewardLimit()
	{
		return df_h()->reward()->getPointsConfig('tag_limit', $this->getReward()->getWebsiteId());
	}

	/**
	 * Return action message for history log
	 *
	 * @param array $args Additional history data
	 * @return string
	 */
	public function getHistoryMessage($args = array())
	{
		$tag = isset($args['tag']) ? $args['tag'] : '';
		return df_h()->reward()->__('For submitting tag (%s).', $tag);
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
			'tag' => $this->getEntity()->getName()
		));
		return $this;
	}
}