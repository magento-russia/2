<?php
/** @method Df_RbkMoney_Model_Payment getMethod() */
class Df_RbkMoney_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/**
	 * @override
	 * @see Df_Payment_Model_Request_Payment::_params()
	 * @used-by Df_Payment_Model_Request_Payment::params()
	 * @return array(string => string|int)
	 */
	protected function _params() {
		/** @var array(string => string) $result */
		$result = array(
			'user_email' => $this->email()
			,'recipientAmount' => $this->amountS()
			,'recipientCurrency' => $this->currencyCode()
			,'orderId' => $this->orderIId()
			,'language' => $this->localeCode()
			,'eshopId' => $this->shopId()
			,'successUrl' => rm_url_checkout_success()
			,'failUrl' => rm_url_checkout_fail()
			,'version' => 2
		);
		if ($this->configS()->getSelectedPaymentMethod()) {
			$result['preference'] = $this->configS()->getSelectedPaymentMethod();
		}
		return $result;
	}
}