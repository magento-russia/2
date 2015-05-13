<?php
class Df_Tweaks_Helper_Customer extends Mage_Core_Helper_Abstract {
	/**
	 * @param Mage_Customer_Model_Customer|null $customer[optional]
	 * @return string
	 */
	public function getFirstNameWithPrefix(Mage_Customer_Model_Customer $customer = null) {
		if (!$customer) {
			$customer = rm_session_customer()->getCustomer();
		}
		$result = '';
		$config = Mage::getSingleton('eav/config');
		/** @var Mage_Eav_Model_Config $config */

		if ($config->getAttribute('customer', 'prefix')->getIsVisible() && $customer->getPrefix()) {
			$result .= $customer->getPrefix() . ' ';
		}
		$result .= $customer->getFirstname();
		return $result;
	}

	/** @return Df_Tweaks_Helper_Customer */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}