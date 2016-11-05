<?php
class Df_Invitation_Model_Resource_Invitation_History extends Df_Core_Model_Resource {
	/**
	 * Нельзя вызывать @see parent::_construct(),
	 * потому что это метод в родительском классе — абстрактный.
	 * @see Mage_Core_Model_Resource_Abstract::_construct()
	 * @override
	 * @return void
	 */
	protected function _construct() {
		$this->_init(self::TABLE, Df_Invitation_Model_Invitation_History::P__ID);
	}
	/** @used-by Df_Invitation_Setup_1_0_0::_process() */
	const TABLE= 'df_invitation/invitation_history';
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}