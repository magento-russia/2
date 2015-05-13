<?php
class Df_Invitation_Model_Observer extends Df_Core_Model_Abstract {
	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function restrictCustomerRegistration(Varien_Event_Observer $observer) {
		if (df_h()->invitation()->config()->isEnabledOnFront()) {
			$result = $observer->getEvent()->getResult();
			if (!$result->getIsAllowed()) {
				df_h()->invitation()->isRegistrationAllowed(false);
			} else {
				df_h()->invitation()->isRegistrationAllowed(true);
				$result->setIsAllowed(!df_h()->invitation()->config()->getInvitationRequired());
			}
		}
	}
}