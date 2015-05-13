<?php
/**
 * @method Df_Interkassa_Model_Payment getPaymentMethod()
 */
class Df_Interkassa_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getParamsInternal() {
		/** @var array(string => string) $result */
		$result =
			array(
				self::REQUEST_VAR__HTTP_METHOD__CONFIRM => self::REQUEST_VALUE__HTTP_METHOD_POST
				,self::REQUEST_VAR__HTTP_METHOD__RETURN_OK => self::REQUEST_VALUE__HTTP_METHOD_POST
				,self::REQUEST_VAR__HTTP_METHOD__RETURN_NO => self::REQUEST_VALUE__HTTP_METHOD_POST
				,self::REQUEST_VAR__ORDER_AMOUNT => $this->getAmount()->getAsString()
				,self::REQUEST_VAR__ORDER_COMMENT => $this->getTransactionDescription()
				,self::REQUEST_VAR__ORDER_NUMBER => $this->getOrder()->getIncrementId()
				,self::REQUEST_VAR__PAYMENT_SERVICE__PAYMENT_METHOD => ''
				,self::REQUEST_VAR__SHOP_ID => $this->getServiceConfig()->getShopId()
				,self::REQUEST_VAR__URL_CONFIRM => $this->getUrlConfirm()
				,self::REQUEST_VAR__URL_RETURN_OK => $this->getUrlCheckoutSuccess()
				,self::REQUEST_VAR__URL_RETURN_NO => $this->getUrlCheckoutFail()
			)
		;
		return $result;
	}

	const REQUEST_VAR__HTTP_METHOD__CONFIRM = 'ik_status_method';
	const REQUEST_VAR__HTTP_METHOD__RETURN_OK = 'ik_success_method';
	const REQUEST_VAR__HTTP_METHOD__RETURN_NO = 'ik_fail_method';
	const REQUEST_VAR__ORDER_AMOUNT = 'ik_payment_amount';
	const REQUEST_VAR__ORDER_COMMENT = 'ik_payment_desc';
	const REQUEST_VAR__ORDER_NUMBER = 'ik_payment_id';
	const REQUEST_VAR__PAYMENT_SERVICE__PAYMENT_METHOD = 'ik_paysystem_alias';
	const REQUEST_VAR__SHOP_ID = 'ik_shop_id';
	const REQUEST_VAR__URL_CONFIRM = 'ik_status_url';
	const REQUEST_VAR__URL_RETURN_OK = 'ik_success_url';
	const REQUEST_VAR__URL_RETURN_NO = 'ik_fail_url';
	const REQUEST_VALUE__HTTP_METHOD_POST = 'POST';
}