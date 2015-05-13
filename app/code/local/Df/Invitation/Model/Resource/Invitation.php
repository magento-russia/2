<?php
class Df_Invitation_Model_Resource_Invitation extends Mage_Core_Model_Mysql4_Abstract {
	/**
	 * @param int $inviterId
	 * @param int $referralId
	 * @return void
	 */
	public function trackReferral($inviterId, $referralId) {
		$inviterId = (int)$inviterId;
		$referralId = (int)$referralId;
		/** @var string $t_TRACK */
		$t_TRACK = rm_table('df_invitation/invitation_track');
		$this->_getWriteAdapter()->query(
			"REPLACE INTO {$t_TRACK} (inviter_id, referral_id)
			VALUES ({$inviterId}, {$referralId})"
		);
	}

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
		$this->_init(self::TABLE_NAME, Df_Invitation_Model_Invitation::P__ID);
	}
	const _CLASS = __CLASS__;
	const TABLE_NAME = 'df_invitation/invitation';
	/**
	 * @see Df_Invitation_Model_Invitation::_construct()
	 * @see Df_Invitation_Model_Resource_Invitation_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Invitation_Model_Resource_Invitation */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}