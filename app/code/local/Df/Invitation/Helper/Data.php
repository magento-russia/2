<?php
class Df_Invitation_Helper_Data extends Mage_Core_Helper_Abstract {
	protected $_isRegistrationAllowed = null;
	/** @return Df_Invitation_Model_Config */
	public function config() {return Df_Invitation_Model_Config::s();}

	/**
	 * Return max Invitation amount per send by config.
	 * Deprecated. Config model 'df_invitation/config' should be used directly.
	 * @return int
	 */
	public function getMaxInvitationsPerSend()
	{
		return $this->config()->getMaxInvitationsPerSend();
	}

	/**
	 * Return config value for required cutomer registration by invitation
	 * Deprecated. Config model 'df_invitation/config' should be used directly.
	 * @return boolean
	 */
	public function getInvitationRequired()
	{
		return $this->config()->getInvitationRequired();
	}

	/**
	 * Return config value for use same group as inviter
	 * Deprecated. Config model 'df_invitation/config' should be used directly.
	 * @return boolean
	 */
	public function getUseInviterGroup()
	{
		return $this->config()->getUseInviterGroup();
	}

	/**
	 * Check whether invitations allow to set custom message
	 * Deprecated. Config model 'df_invitation/config' should be used directly.
	 * @return bool
	 */
	public function isInvitationMessageAllowed()
	{
		return $this->config()->isInvitationMessageAllowed();
	}

	/**
	 * Return text for invetation status
	 * @return Df_Invitation_Model_Invitation $invitation
	 * @return string
	 */
	public function getInvitationStatusText($invitation)
	{
		return Df_Invitation_Model_Source_Invitation_Status::s()->getOptionText($invitation->getStatus());
	}

	/**
	 * Return invitation url
	 *
	 * @param Df_Invitation_Model_Invitation $invitation
	 * @return string
	 */
	public function getInvitationUrl($invitation) {
		return
			Df_Core_Model_Url::i()
				->setStore($invitation->getStoreId())
				->getUrl(
					'df_invitation/customer_account/create'
					,array(
						'invitation' =>
							df_mage()->coreHelper()->urlEncode(
								$invitation->getInvitationCode()
							)
							,'_store_to_url' => true
					)
				)
		;
	}

	/**
	 * Return account dashboard invitation url
	 * @return string
	 */
	public function getCustomerInvitationUrl()
	{
		return $this->_getUrl('df_invitation/');
	}

	/**
	 * Return invitation send form url
	 * @return string
	 */
	public function getCustomerInvitationFormUrl()
	{
		return $this->_getUrl('df_invitation/index/send');
	}

	/**
	 * Checks is allowed registration in invitation controller
	 *
	 * @param boolean $isAllowed
	 * @return boolean
	 */
	public function isRegistrationAllowed($isAllowed = null)
	{
		if ($isAllowed === null && $this->_isRegistrationAllowed === null) {
			$result = df_customer_h()->isRegistrationAllowed();
			if ($this->_isRegistrationAllowed === null) {
				$this->_isRegistrationAllowed = $result;
			}
		} else if ($isAllowed !== null) {
			$this->_isRegistrationAllowed = $isAllowed;
		}
		return $this->_isRegistrationAllowed;
	}

	/**
	 * Retrieve configuration for availability of invitations
	 * Deprecated. Config model 'df_invitation/config' should be used directly.
	 * @return boolean
	 */
	public function isEnabled()
	{
		return $this->config()->isEnabled();
	}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}