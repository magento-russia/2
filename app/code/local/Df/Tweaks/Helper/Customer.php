<?php
class Df_Tweaks_Helper_Customer extends Mage_Core_Helper_Abstract {
	/**
	 * @param Df_Customer_Model_Customer|null $customer optional]
	 * @return string
	 */
	public function getFirstNameWithPrefix(Df_Customer_Model_Customer $customer = null) {
		if (!$customer) {
			$customer = df_session_customer()->getCustomer();
		}
		$result = '';
		/** @var Mage_Eav_Model_Config $config */
		$config = df_mage()->eav()->configSingleton();
		/** @noinspection PhpUndefinedMethodInspection */
		if ($config->getAttribute('customer', 'prefix')->getIsVisible() && $customer->getPrefix()) {
			/** @noinspection PhpUndefinedMethodInspection */
			$result .= $customer->getPrefix() . ' ';
		}
		$result .= $customer->getFirstname();
		return $result;
	}

	/** @return Df_Tweaks_Helper_Customer */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}