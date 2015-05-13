<?php
class Df_Logging_Model_Resource_Event_Collection
	extends Mage_Core_Model_Mysql4_Collection_Abstract {
	/**
	 * Minimize usual count select
	 * @return Varien_Db_Select
	 */
	public function getSelectCountSql() {
		return parent::getSelectCountSql()->resetJoinLeft();
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Logging_Model_Event::mf(), Df_Logging_Model_Resource_Event::mf());
	}
	const _CLASS = __CLASS__;
	/** @return Df_Logging_Model_Resource_Event_Collection */
	public static function i() {return new self;}
}