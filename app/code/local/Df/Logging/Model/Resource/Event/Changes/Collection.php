<?php
class Df_Logging_Model_Resource_Event_Changes_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Logging_Model_Event_Changes::mf(), Df_Logging_Model_Resource_Event_Changes::mf());
	}
	const _CLASS = __CLASS__;
	/** @return Df_Logging_Model_Resource_Event_Changes_Collection */
	public static function i() {return new self;}
}