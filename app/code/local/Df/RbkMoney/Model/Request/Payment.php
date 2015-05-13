<?php
/**
 * @method Df_RbkMoney_Model_Payment getPaymentMethod()
 */
class Df_RbkMoney_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getParamsInternal() {
		/** @var array(string => string) $result */
		$result =
			array(
				self::REQUEST_VAR__CUSTOMER__EMAIL => $this->getCustomerEmail()
				,self::REQUEST_VAR__ORDER_AMOUNT => $this->getAmount()->getAsString()
				,self::REQUEST_VAR__ORDER_CURRENCY =>
					$this->getServiceConfig()->getCurrencyCodeInServiceFormat()
				,self::REQUEST_VAR__ORDER_NUMBER => $this->getOrder()->getIncrementId()
				,self::REQUEST_VAR__PAYMENT_SERVICE__LANGUAGE =>
					$this->getServiceConfig()->getLocaleCodeInServiceFormat()
				,self::REQUEST_VAR__SHOP_ID => $this->getServiceConfig()->getShopId()
				,self::REQUEST_VAR__URL_RETURN_OK =>	$this->getUrlCheckoutSuccess()
				,self::REQUEST_VAR__URL_RETURN_NO =>	$this->getUrlCheckoutFail()
				,self::REQUEST_VAR__PAYMENT_SERVICE__PROTOCOL_VERSION => 2
			)
		;
		if (!is_null($this->getServiceConfig()->getSelectedPaymentMethod())) {
			$result[self::REQUEST_VAR__SPECIFIC_PAYMENT_METHOD] =
				$this->getServiceConfig()->getSelectedPaymentMethod()
			;
		}
		return $result;
	}

	const REQUEST_VAR__CUSTOMER__EMAIL = 'user_email';
	const REQUEST_VAR__ORDER_AMOUNT = 'recipientAmount';
	const REQUEST_VAR__ORDER_COMMENT = 'serviceName';
	const REQUEST_VAR__ORDER_CURRENCY = 'recipientCurrency';
	const REQUEST_VAR__ORDER_NUMBER = 'orderId';
	const REQUEST_VAR__PAYMENT_SERVICE__LANGUAGE = 'language';
	const REQUEST_VAR__PAYMENT_SERVICE__PROTOCOL_VERSION = 'version';
	const REQUEST_VAR__SHOP_ID = 'eshopId';
	const REQUEST_VAR__SPECIFIC_PAYMENT_METHOD = 'preference';
	const REQUEST_VAR__URL_RETURN_OK = 'successUrl';
	const REQUEST_VAR__URL_RETURN_NO = 'failUrl';
}