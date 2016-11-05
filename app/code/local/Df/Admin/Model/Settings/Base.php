<?php
class Df_Admin_Model_Settings_Base extends Df_Core_Model_Settings {
	/**
	 * @param Df_Core_Model_StoreM|int|string|bool|null $store [optional]
	 * @return string
	 */
	public function getStorePhone($store = null) {
		return $this->v(Mage_Core_Model_Store::XML_PATH_STORE_STORE_PHONE, $store);
	}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}