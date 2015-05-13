<?php
class Df_Masterbank_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getParamsInternal() {
		return array(
			'AMOUNT' => $this->getAmount()->getAsString()
			,'ORDER' => $this->getOrder()->getIncrementId()
			,'MERCH_URL' => $this->getUrlCheckoutSuccess()
			,'TERMINAL' => $this->getServiceConfig()->getShopId()
			,'TIMESTAMP' => Df_Masterbank_Helper_Data::s()->getTimestamp()
			,'SIGN' => Df_Masterbank_Helper_Data::s()->getSignature($this)
			,'LANGUAGE' => 'rus'
		);
	}
}