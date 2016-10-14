<?php
class Df_Sms_Model_Settings_General extends Df_Core_Model_Settings {
	/**
	 * @param Df_Core_Model_StoreM $store
	 * @return string
	 */
	public function getAdministratorPhone(Df_Core_Model_StoreM $store) {
		return $this->getString('administrator_phone', $store);
	}
	/**
	 * @param Df_Core_Model_StoreM $store
	 * @return boolean
	 */
	public function getGate(Df_Core_Model_StoreM $store) {return $store->getConfig('gate');}
	/**
	 * @param Df_Core_Model_StoreM $store
	 * @return string
	 */
	public function getGateClass(Df_Core_Model_StoreM $store) {
		return dfa(
			array(Df_Sms_Model_Gate_Sms16Ru::RM__ID => Df_Sms_Model_Gate_Sms16Ru::_C)
			,$this->getGate($store)
		);
	}
	/**
	 * @param Df_Core_Model_StoreM $store
	 * @return string
	 */
	public function getSender(Df_Core_Model_StoreM $store) {return $this->getString('sender', $store);}
	/**
	 * @param Df_Core_Model_StoreM $store
	 * @return boolean
	 */
	public function isEnabled(Df_Core_Model_StoreM $store) {return $this->getYesNo('enabled');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_sms/general/';}
	/** @return Df_Sms_Model_Settings_General */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}