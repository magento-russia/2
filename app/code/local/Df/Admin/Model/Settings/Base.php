<?php
class Df_Admin_Model_Settings_Base extends Df_Core_Model_Settings {
	/**
	 * @param mixed $store[optional]
	 * @return string
	 */
	public function getStorePhone($store = null) {
		return $this->getStringNullable(Mage_Core_Model_Store::XML_PATH_STORE_STORE_PHONE, $store);
	}
	/** @return Df_Admin_Model_Settings_Base */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}