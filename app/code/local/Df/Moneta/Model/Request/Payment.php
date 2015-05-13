<?php
/**
 * @method Df_Moneta_Model_Payment getPaymentMethod()
 */
class Df_Moneta_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getParamsInternal() {
		/** @var array(string => string) $result */
		$result = array(
			self::REQUEST_VAR__ORDER_AMOUNT => $this->getAmount()->getAsString()
			,self::REQUEST_VAR__ORDER_COMMENT => $this->getTransactionDescription()
			,self::REQUEST_VAR__ORDER_CURRENCY =>
				$this->getServiceConfig()->getCurrencyCodeInServiceFormat()
			,self::REQUEST_VAR__ORDER_NUMBER => $this->getOrder()->getIncrementId()
			,self::REQUEST_VAR__PAYMENT_SERVICE__PAYMENT_METHOD =>
				$this->getServiceConfig()->getSelectedPaymentMethodCode()
			,self::REQUEST_VAR__PAYMENT_SERVICE__PAYMENT_METHODS => $this->getPaymentMethodsAllowed()
			,self::REQUEST_VAR__REQUEST__TEST_MODE => rm_01($this->getPaymentMethod()->isTestMode())
			,self::REQUEST_VAR__SIGNATURE =>	$this->getSignature()
			,self::REQUEST_VAR__SHOP_ID => $this->getServiceConfig()->getShopId()
			,self::REQUEST_VAR__URL_RETURN_OK =>	$this->getUrlCheckoutSuccess()
			,self::REQUEST_VAR__URL_RETURN_NO =>	$this->getUrlCheckoutFail()
		);
		return $result;
	}

	/** @return string */
	private function getPaymentMethodsAllowed() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				implode(',', $this->getServiceConfig()->getSelectedPaymentMethodCodes())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getSignature() {
		return md5(implode($this->preprocessParams(array(
			self::REQUEST_VAR__SHOP_ID => $this->getServiceConfig()->getShopId()
			,self::REQUEST_VAR__ORDER_NUMBER => $this->getOrder()->getIncrementId()
			,self::REQUEST_VAR__ORDER_AMOUNT => $this->getAmount()->getAsString()
			,self::REQUEST_VAR__ORDER_CURRENCY => $this->getServiceConfig()->getCurrencyCodeInServiceFormat()
			,self::REQUEST_VAR__REQUEST__TEST_MODE => rm_01($this->getPaymentMethod()->isTestMode())
			,self::SIGNATURE_PARAM__ENCRYPTION_KEY =>
				$this->getServiceConfig()->getResponsePassword()
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