<?php
/** @method Df_Assist_Model_Payment getMethod() */
class Df_Assist_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/**
	 * @override
	 * @see Df_Payment_Model_Request_Payment::_params()
	 * @used-by Df_Payment_Model_Request_Payment::params()
	 * @return array(string => string|int)
	 */
	protected function _params() {
		return array_fill_keys($this->configS()->getDisabledPaymentMethods(), 0) + array(
			'Address' => $this->street()
			,'City' => $this->city()
			,'Country' => $this->iso3()
			,'Email' => $this->email()
			,'Firstname' => $this->nameFirst()
			,'Lastname' => $this->nameLast()
			,'Middlename' => $this->nameMiddle()
			,'HomePhone' => $this->phone()
			,'State' => $this->regionCode()
			,'Zip' => $this->postCode()
			,'OrderAmount' => $this->amountS()
			,'OrderCurrency' => $this->currencyCode()
			,'OrderNumber' => $this->orderIId()
			,'Delay' => df_01($this->configS()->isCardPaymentActionAuthorize())
			,'Language' => $this->localeCode()
			,'TestMode' => df_01($this->getMethod()->isTestMode())
			,'RecurringIndicator' => 0
			,'Merchant_ID' => $this->shopId()
			,'URL_RETURN_OK' => rm_url_checkout_success()
			,'URL_RETURN_NO' => rm_url_checkout_fail()
		);
	}
}