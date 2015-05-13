<?php
/**
 * Reward action for updating balance by salesrule
 */
class Df_Reward_Model_Action_Salesrule extends Df_Reward_Model_Action_Abstract {
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
		$incrementId = isset($args['increment_id']) ? $args['increment_id'] : '';
		return df_h()->reward()->__('Earned promotion extra points from order #%s.', $incrementId);
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
			'increment_id' => $this->getEntity()->getIncrementId()
		));
		return $this;
	}
}