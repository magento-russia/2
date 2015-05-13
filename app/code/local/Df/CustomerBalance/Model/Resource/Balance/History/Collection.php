<?php
class Df_CustomerBalance_Model_Resource_Balance_History_Collection
	extends Mage_Core_Model_Mysql4_Collection_Abstract {
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
	 * @return Df_CustomerBalance_Model_Resource_Balance_History_Collection
	 */
	protected function _initSelect() {
		parent::_initSelect();
		$this->getSelect()
			->joinInner(
				array('b' => rm_table('df_customerbalance/balance'))
				,'main_table.balance_id = b.balance_id'
				,array(
					'customer_id' => 'b.customer_id'
					,'website_id' => 'b.website_id'
					,'base_currency_code' => 'b.base_currency_code'
				)
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
		$this->_init(
			Df_CustomerBalance_Model_Balance_History::mf()
			,Df_CustomerBalance_Model_Resource_Balance_History::mf()
		);
	}
	const _CLASS = __CLASS__;

	/** @return Df_CustomerBalance_Model_Resource_Balance_History_Collection */
	public static function i() {return new self;}
}