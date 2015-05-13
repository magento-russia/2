<?php
class Df_CustomerBalance_Helper_Data extends Mage_Core_Helper_Abstract {
	/**
	 * Check whether customer balance functionality should be enabled
	 * @return bool
	 */
	public function isEnabled() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
					Mage::isInstalled()
				&&
					df_enabled(Df_Core_Feature::CUSTOMER_BALANCE)
				&&
					df_cfg()->customer()->balance()->isEnabled()
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_CustomerBalance_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}