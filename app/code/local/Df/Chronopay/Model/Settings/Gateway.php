<?php
class Df_Chronopay_Model_Settings_Gateway extends Df_Core_Model_Settings {
	/** @return string */
	public function getTransactionCurrency() {
		return $this->getString('df_payment/chronopay_gate/transaction_currency');
	}
	/**
	 * @static
	 * @param Df_Core_Model_StoreM $store
	 * @return Df_Chronopay_Model_Settings_Gateway
	 */
	public static function i(Df_Core_Model_StoreM $store) {return new self(array(self::P__STORE => $store));}
}