<?php
class Df_Sms_Model_Settings_Sms16Ru extends Df_Core_Model_Settings {
	/**
	 * @param Mage_Core_Model_Store $store
	 * @return string
	 */
	public function getToken(Mage_Core_Model_Store $store) {
		return $this->getPassword('df_sms/sms16_ru/token', $store);
	}
	/** @return Df_Sms_Model_Settings_Sms16Ru */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}