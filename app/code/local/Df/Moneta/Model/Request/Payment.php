<?php
/** @method Df_Moneta_Model_Payment getMethod() */
class Df_Moneta_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/**
	 * @override
	 * @see Df_Payment_Model_Request_Payment::_params()
	 * @used-by Df_Payment_Model_Request_Payment::params()
	 * @return array(string => string|int)
	 */
	protected function _params() {
		/** @var array(string => string) $result */
		$result = array(
			self::REQUEST_VAR__ORDER_AMOUNT => $this->amountS()
			,self::REQUEST_VAR__ORDER_COMMENT => $this->getTransactionDescription()
			,self::REQUEST_VAR__ORDER_CURRENCY =>
				$this->configS()->getCurrencyCodeInServiceFormat()
			,self::REQUEST_VAR__ORDER_NUMBER => $this->orderIId()
			,self::REQUEST_VAR__PAYMENT_SERVICE__PAYMENT_METHOD =>
				$this->configS()->getSelectedPaymentMethodCode()
			,self::REQUEST_VAR__PAYMENT_SERVICE__PAYMENT_METHODS => $this->getPaymentMethodsAllowed()
			,self::REQUEST_VAR__REQUEST__TEST_MODE => rm_01($this->getMethod()->isTestMode())
			,self::REQUEST_VAR__SIGNATURE => $this->getSignature()
			,self::REQUEST_VAR__SHOP_ID => $this->shopId()
			,self::REQUEST_VAR__URL_RETURN_OK => rm_url_checkout_success()
			,self::REQUEST_VAR__URL_RETURN_NO => rm_url_checkout_fail()
		);
		return $result;
	}

	/** @return string */
	private function getPaymentMethodsAllowed() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_csv($this->configS()->getSelectedPaymentMethodCodes());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getSignature() {
		return md5(implode($this->preprocessParams(array(
			self::REQUEST_VAR__SHOP_ID => $this->shopId()
			,self::REQUEST_VAR__ORDER_NUMBER => $this->orderIId()
			,self::REQUEST_VAR__ORDER_AMOUNT => $this->amountS()
			,self::REQUEST_VAR__ORDER_CURRENCY => $this->configS()->getCurrencyCodeInServiceFormat()
			,self::REQUEST_VAR__REQUEST__TEST_MODE => rm_01($this->getMethod()->isTestMode())
			,self::SIGNATURE_PARAM__ENCRYPTION_KEY => $this->password()
		))));
	}

	const REQUEST_VAR__ORDER_AMOUNT = 'MNT_AMOUNT';
	const REQUEST_VAR__ORDER_COMMENT = 'MNT_DESCRIPTION';
	const REQUEST_VAR__ORDER_CURRENCY = 'MNT_CURRENCY_CODE';
	const REQUEST_VAR__ORDER_NUMBER = 'MNT_TRANSACTION_ID';
	const REQUEST_VAR__PAYMENT_SERVICE__LANGUAGE = 'moneta.locale';
	const REQUEST_VAR__PAYMENT_SERVICE__PAYMENT_METHOD = 'paymentSystem.unitId';
	const REQUEST_VAR__PAYMENT_SERVICE__PAYMENT_METHODS = 'paymentSystem.limitIds';
	const REQUEST_VAR__REQUEST__TEST_MODE = 'MNT_TEST_MODE';
	const REQUEST_VAR__SIGNATURE = 'MNT_SIGNATURE';
	const REQUEST_VAR__SHOP_ID = 'MNT_ID';
	const REQUEST_VAR__URL_RETURN_OK = 'MNT_SUCCESS_URL';
	const REQUEST_VAR__URL_RETURN_NO = 'MNT_FAIL_URL';
	const SIGNATURE_PARAM__ENCRYPTION_KEY = 'encryption_key';
}