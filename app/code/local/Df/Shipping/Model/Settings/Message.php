<?php
class Df_Shipping_Model_Settings_Message extends Df_Core_Model_Settings {
	/**
	 * @param mixed $store[optional]
	 * @return string
	 */
	public function getFailureGeneral($store = null) {return $this->getString('general', $store);}

	/**
	 * @param mixed $store[optional]
	 * @return string
	 */
	public function getFailureSameLocation($store = null) {return $this->getString('same_location', $store);}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_shipping/message/failure__';}
	/** @return Df_Shipping_Model_Settings_Message */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}