<?php
class Df_CustomerBalance_Model_Resource_Balance_Collection
	extends Mage_Core_Model_Mysql4_Collection_Abstract {
	/**
	 * @param array|int $websiteIds
	 * @return Df_CustomerBalance_Model_Resource_Balance_Collection
	 */
	public function addWebsitesFilter($websiteIds) {
		$this->getSelect()->where(rm_quote_into('main_table.website_id IN (?)', $websiteIds));
		return $this;
	}

	/**
	 * @override
	 * @return Df_CustomerBalance_Model_Resource_Balance_Collection
	 */
	protected function _afterLoad() {
		parent::_afterLoad();
		$this->walk('afterLoad');
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_CustomerBalance_Model_Balance::mf(), Df_CustomerBalance_Model_Resource_Balance::mf());
	}
	const _CLASS = __CLASS__;
	/** @return Df_CustomerBalance_Model_Resource_Balance_Collection */
	public static function i() {return new self;}
}