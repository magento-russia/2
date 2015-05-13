<?php
class Df_Logging_Model_Resource_Event_Changes extends Mage_Core_Model_Mysql4_Abstract {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		/**
		 * Нельзя вызывать parent::_construct(),
		 * потому что это метод в родительском классе — абстрактный.
		 * @see Mage_Core_Model_Resource_Abstract::_construct()
		 */
		$this->_init(self::TABLE_NAME, Df_Logging_Model_Event_Changes::P__ID);
	}
	const _CLASS = __CLASS__;
	const TABLE_NAME = 'df_logging/event_changes';
	/**
	 * @see Df_Logging_Model_Event_Changes::_construct()
	 * @see Df_Logging_Model_Resource_Event_Changes_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Logging_Model_Resource_Event_Changes */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}