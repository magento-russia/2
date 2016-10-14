<?php
class Df_Logging_Model_Resource_Event_Changes_Collection extends Df_Core_Model_Resource_Collection {
	/**
	 * @override
	 * @return Df_Logging_Model_Resource_Event_Changes
	 */
	public function getResource() {return Df_Logging_Model_Resource_Event_Changes::s();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_itemObjectClass = Df_Logging_Model_Event_Changes::_C;}
	const _C = __CLASS__;
}