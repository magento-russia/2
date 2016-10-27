<?php
/** @method Df_RbkMoney_Method method() */
class Df_RbkMoney_Request_Payment extends \Df\Payment\Request\Payment {
	/**
	 * @override
	 * @see \Df\Payment\Request\Payment::_params()
	 * @used-by \Df\Payment\Request\Payment::params()
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
			,'successUrl' => df_url_checkout_success()
			,'failUrl' => df_url_checkout_fail()
			,'version' => 2
		);
		if ($this->configS()->getSelectedPaymentMethod()) {
			$result['preference'] = $this->configS()->getSelectedPaymentMethod();
		}
		return $result;
	}
}