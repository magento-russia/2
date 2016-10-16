<?php
class Df_Invitation_Model_Resource_Invitation extends Df_Core_Model_Resource {
	/**
	 * @param int $inviterId
	 * @param int $referralId
	 * @return void
	 */
	public function trackReferral($inviterId, $referralId) {
		$inviterId = (int)$inviterId;
		$referralId = (int)$referralId;
		/** @var string $t_TRACK */
		$t_TRACK = df_table('df_invitation/invitation_track');
		$this->_getWriteAdapter()->query(
			"REPLACE INTO {$t_TRACK} (inviter_id, referral_id)
			VALUES ({$inviterId}, {$referralId})"
		);
	}

	/**
	 * Нельзя вызывать @see parent::_construct(),
	 * потому что это метод в родительском классе — абстрактный.
	 * @see Mage_Core_Model_Resource_Abstract::_construct()
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_init(self::TABLE, Df_Invitation_Model_Invitation::P__ID);}
	/** @used-by Df_Invitation_Setup_1_0_0::_process() */
	const TABLE = 'df_invitation/invitation';
	/** @return Df_Invitation_Model_Resource_Invitation */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}