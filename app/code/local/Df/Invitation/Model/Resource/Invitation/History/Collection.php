<?php
class Df_Invitation_Model_Resource_Invitation_History_Collection
	extends Mage_Core_Model_Mysql4_Collection_Abstract {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(
			Df_Invitation_Model_Invitation_History::mf()
			,Df_Invitation_Model_Resource_Invitation_History::mf()
		);
	}
	const _CLASS = __CLASS__;

	/** @return Df_Invitation_Model_Resource_Invitation_History_Collection */
	public static function i() {return new self;}
}