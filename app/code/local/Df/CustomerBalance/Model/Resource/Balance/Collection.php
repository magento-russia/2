<?php
class Df_CustomerBalance_Model_Resource_Balance_Collection extends Df_Core_Model_Resource_Collection {
	/**
	 * @param array|int $websiteIds
	 * @return Df_CustomerBalance_Model_Resource_Balance_Collection
	 */
	public function addWebsitesFilter($websiteIds) {
		$this->getSelect()->where(df_db_quote_into('main_table.website_id IN (?)', $websiteIds));
		return $this;
	}

	/**
	 * @override
	 * @return Df_CustomerBalance_Model_Resource_Balance
	 */
	public function getResource() {return Df_CustomerBalance_Model_Resource_Balance::s();}

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
	protected function _construct() {$this->_itemObjectClass = Df_CustomerBalance_Model_Balance::_C;}
	const _C = __CLASS__;
}