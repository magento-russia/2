<?php
class Df_Invitation_Model_Config {
	const XML_PATH_ENABLED = 'df_invitation/general/enabled';
	const XML_PATH_ENABLED_ON_FRONT = 'df_invitation/general/enabled_on_front';
	const XML_PATH_USE_INVITATION_MESSAGE = 'df_invitation/general/allow_customer_message';
	const XML_PATH_MAX_INVITATION_AMOUNT_PER_SEND = 'df_invitation/general/max_invitation_amount_per_send';
	const XML_PATH_REGISTRATION_REQUIRED_INVITATION = 'df_invitation/general/registration_required_invitation';
	const XML_PATH_REGISTRATION_USE_INVITER_GROUP = 'df_invitation/general/registration_use_inviter_group';

	/**
	 * Return max Invitation amount per send by config
	 *
	 * @param int $storeId
	 * @return int
	 */
	public function getMaxInvitationsPerSend($storeId = null) {
		$max = (int)Mage::getStoreConfig(self::XML_PATH_MAX_INVITATION_AMOUNT_PER_SEND, $storeId);
		return($max < 1 ? 1 : $max);
	}

	/**
	 * Return config value for required cutomer registration by invitation
	 *
	 * @param int $storeId
	 * @return boolean
	 */
	public function getInvitationRequired($storeId = null)
	{
		return Mage::getStoreConfig(self::XML_PATH_REGISTRATION_REQUIRED_INVITATION, $storeId);
	}

	/**
	 * Return config value for use same group as inviter
	 *
	 * @param int $storeId
	 * @return boolean
	 */
	public function getUseInviterGroup($storeId = null)
	{
		return Mage::getStoreConfigFlag(self::XML_PATH_REGISTRATION_USE_INVITER_GROUP, $storeId);
	}

	/**
	 * Check whether invitations allow to set custom message
	 *
	 * @param int $storeId
	 * @return bool
	 */
	public function isInvitationMessageAllowed($storeId = null)
	{
		return Mage::getStoreConfigFlag(self::XML_PATH_USE_INVITATION_MESSAGE, $storeId);
	}

	/**
	 * Retrieve configuration for availability of invitations
	 * on global level. Also will disallowe any functionality in admin.
	 *
	 * @param int $storeId[optional]
	 * @return boolean
	 */
	public function isEnabled($storeId = null) {
		if (!is_null($storeId)) {
			$storeId = rm_nat0($storeId);
		}
		else {
			/** @var int $defaultStoreId */
			static $defaultStoreId;
			if (!isset($defaultStoreId)) {
				$defaultStoreId = rm_nat0(Mage::app()->getStore()->getId());
			}
			$storeId = $defaultStoreId;
		}
		if (!isset($this->{__METHOD__}[$storeId])) {
			/** @var bool $featureIsEnabled */
			static $featureIsEnabled;
			if (!isset($featureIsEnabled)) {
				$featureIsEnabled = df_enabled(Df_Core_Feature::INVITATION);
			}
			$this->{__METHOD__}[$storeId] =
				$featureIsEnabled && Mage::getStoreConfigFlag(self::XML_PATH_ENABLED, $storeId)
			;
		}
		return $this->{__METHOD__}[$storeId];
	}

	/**
	 * Retrieve configuration for availability of invitations
	 * on front for specified store. Global parameter 'enabled' has more priority.
	 * @param int $storeId
	 * @return boolean
	 */
	public function isEnabledOnFront($storeId = null)
	{
		if ($this->isEnabled($storeId)) {
			return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED_ON_FRONT, $storeId);
		}
		return false;
	}

	/** @return Df_Invitation_Model_Config */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}