<?php
class Df_Reward_Model_Resource_Reward_Collection extends Df_Core_Model_Resource_Collection {
	/**
	 * @param int|int[] $websiteId
	 * @return Df_Reward_Model_Resource_Reward_Collection
	 */
	public function addWebsiteFilter($websiteId) {
		$this->getSelect()->where('main_table.website_id IN (?)', df_array($websiteId));
		return $this;
	}

	/**
	 * @override
	 * @return Df_Reward_Model_Resource_Reward
	 */
	public function getResource() {return Df_Reward_Model_Resource_Reward::s();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_itemObjectClass = Df_Reward_Model_Reward::_C;}
	const _C = __CLASS__;
}