<?php
class Df_Reward_Model_Resource_Reward_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
	/**
	 * Add filter by website id
	 * @param integer|array $websiteId
	 * @return Df_Reward_Model_Resource_Reward_Collection
	 */
	public function addWebsiteFilter($websiteId) {
		$this->getSelect()
			->where(
				is_array($websiteId)
				? 'main_table.website_id IN (?)'
				: 'main_table.website_id = ?'
				,$websiteId
			)
		;
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Reward_Model_Reward::mf(), Df_Reward_Model_Resource_Reward::mf());
	}
	const _CLASS = __CLASS__;

	/** @return Df_Reward_Model_Resource_Reward_Collection */
	public static function i() {return new self;}
}