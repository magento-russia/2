<?php
class Df_Invitation_Observer extends Df_Core_Model {
	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @see Mage_Customer_Helper_Data::isRegistrationAllowed()
		Mage::dispatchEvent('customer_registration_is_allowed', array('result' => $result));
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function customer_registration_is_allowed(Varien_Event_Observer $o) {
		if (df_h()->invitation()->config()->isEnabledOnFront()) {
			/**
			 * @var Varien_Object $result
			 * @see @see Mage_Customer_Helper_Data::isRegistrationAllowed()
					$result = new Varien_Object(array('is_allowed' => true));
			 */
			$result = $o['result'];
			if (!$result['is_allowed']) {
				df_h()->invitation()->isRegistrationAllowed(false);
			}
			else {
				df_h()->invitation()->isRegistrationAllowed(true);
				$result['is_allowed'] = !df_h()->invitation()->config()->getInvitationRequired();
			}
		}
	}
}