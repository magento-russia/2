<?php
/**
 * Reward Helper for operations with customer
 */
class Df_Reward_Helper_Customer extends Mage_Core_Helper_Abstract {
	/**
	 * @param string|bool $notification [optional]
	 * @return string
	 */
	public function getUnsubscribeUrl($notification = false)
	{
		$params = [];
		if ($notification) {
			$params = array('notification' => $notification);
		}
		return Mage::getUrl('df_reward/customer/unsubscribe/', array('notification' => $notification));
	}

	/** @return Df_Reward_Helper_Customer */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}