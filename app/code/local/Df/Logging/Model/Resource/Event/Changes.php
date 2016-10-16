<?php
class Df_Logging_Model_Resource_Event_Changes extends Df_Core_Model_Resource {
	/**
	 * Нельзя вызывать @see parent::_construct(),
	 * потому что это метод в родительском классе — абстрактный.
	 * @see Mage_Core_Model_Resource_Abstract::_construct()
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_init(self::TABLE, Df_Logging_Model_Event_Changes::P__ID);}
	/**
	 * @used-by Df_Logging_Model_Resource_Event::getEventChangeIds()
	 * @used-by Df_Logging_Setup_1_0_0::_process()
	 */
	const TABLE = 'df_logging/event_changes';
	/** @return Df_Logging_Model_Resource_Event_Changes */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}