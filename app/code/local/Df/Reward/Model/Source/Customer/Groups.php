<?php
/**
 * Reward Customer Groups source model
 */
class Df_Reward_Model_Source_Customer_Groups
{
	/**
	 * Retrieve option array of customer groups
	 * @return array
	 */
	public function toOptionArray()
	{
		$groups = Mage::getResourceModel('customer/group_collection')
			->addFieldToFilter('customer_group_id', array('gt'=> 0))
			->load()
			->toOptionHash();
		$groups = array(0 => df_h()->reward()->__('All Customer Groups'))
				+ $groups;
		return $groups;
	}

	/** @return Df_Reward_Model_Source_Customer_Groups */
	public static function i() {return new self;}
}