<?php
class Df_CustomerBalance_Model_Resource_Balance_History_Collection
	extends Df_Core_Model_Resource_Collection {
	/**
	 * @param array|int $websiteIds
	 * @return Df_CustomerBalance_Model_Resource_Balance_History_Collection
	 */
	public function addWebsitesFilter($websiteIds) {
		$this->getSelect()->where(rm_quote_into('b.website_id IN (?)', $websiteIds));
		return $this;
	}

	/**
	 * @override
	 * @return Df_CustomerBalance_Model_Resource_Balance_History
	 */
	public function getResource() {return Df_CustomerBalance_Model_Resource_Balance_History::s();}

	/**
	 * @override
	 * @return Df_CustomerBalance_Model_Resource_Balance_History_Collection
	 */
	protected function _initSelect() {
		parent::_initSelect();
		$this->getSelect()->joinInner(
			array('b' => rm_table(Df_CustomerBalance_Model_Resource_Balance::TABLE))
			,'main_table.balance_id = b.balance_id'
			,array(
				'customer_id' => 'b.customer_id'
				,'website_id' => 'b.website_id'
				,'base_currency_code' => 'b.base_currency_code'
			)
		);
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		$this->_itemObjectClass = Df_CustomerBalance_Model_Balance_History::_C;
	}
	const _C = __CLASS__;
}