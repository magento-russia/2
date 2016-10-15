<?php
class Df_Logging_Model_Resource_Event_Collection extends Df_Core_Model_Resource_Collection {
	/**
	 * @override
	 * @return Df_Logging_Model_Resource_Event
	 */
	public function getResource() {return Df_Logging_Model_Resource_Event::s();}

	/**
	 * Minimize usual count select
	 * @return Varien_Db_Select
	 */
	public function getSelectCountSql() {return parent::getSelectCountSql()->resetJoinLeft();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_itemObjectClass = Df_Logging_Model_Event::class;}

}