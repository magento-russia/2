<?php
class Df_Client_Model_Resource_DelayedMessage_Collection
	extends Mage_Core_Model_Mysql4_Collection_Abstract {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Client_Model_DelayedMessage::mf(), Df_Client_Model_Resource_DelayedMessage::mf());
	}
	const _CLASS = __CLASS__;

	/** @return Df_Client_Model_Resource_DelayedMessage_Collection */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}