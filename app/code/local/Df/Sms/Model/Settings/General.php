<?php
class Df_Sms_Model_Settings_General extends Df_Core_Model_Settings {
	/**
	 * @param Mage_Core_Model_Store $store
	 * @return string
	 */
	public function getAdministratorPhone(Mage_Core_Model_Store $store) {
		return $this->getString('administrator_phone', $store);
	}
	/**
	 * @param Mage_Core_Model_Store $store
	 * @return boolean
	 */
	public function getGate(Mage_Core_Model_Store $store) {
		return Mage::getStoreConfig('gate', $store);
	}
	/**
	 * @param Mage_Core_Model_Store $store
	 * @return string
	 */
	public function getGateClass(Mage_Core_Model_Store $store) {
		/** @var string $result */
		$result =
			df_a(
				array(Df_Sms_Model_Gate_Sms16Ru::RM__ID => Df_Sms_Model_Gate_Sms16Ru::_CLASS)
				,$this->getGate($store)
			)
		;
		df_result_string($result);
		return $result;
	}
	/**
	 * @param Mage_Core_Model_Store $store
	 * @return string
	 */
	public function getSender(Mage_Core_Model_Store $store) {
		return $this->getString('sender', $store);
	}
	/**
	 * @param Mage_Core_Model_Store $store
	 * @return boolean
	 */
	public function isEnabled(Mage_Core_Model_Store $store) {
		return $this->getYesNo('enabled');
	}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_sms/general/';}
	/** @return Df_Sms_Model_Settings_General */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}