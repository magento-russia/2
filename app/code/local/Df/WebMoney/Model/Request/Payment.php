<?php
/**
 * @method Df_WebMoney_Model_Config_Area_Service configS()
 * @method Df_WebMoney_Model_Payment getMethod()
 */
class Df_WebMoney_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/**
	 * @override
	 * @see Df_Payment_Model_Request_Payment::_params()
	 * @used-by Df_Payment_Model_Request_Payment::params()
	 * @return array(string => string|int)
	 */
	protected function _params() {
		return array(
			self::REQUEST_VAR__CUSTOMER__EMAIL => $this->email()
			,self::REQUEST_VAR__HTTP_METHOD__RETURN_OK => self::REQUEST_VALUE__HTTP_METHOD_POST
			,self::REQUEST_VAR__HTTP_METHOD__RETURN_NO => self::REQUEST_VALUE__HTTP_METHOD_POST
			,self::REQUEST_VAR__ORDER_AMOUNT => $this->amountS()
			,self::REQUEST_VAR__ORDER_COMMENT => base64_encode($this->getTransactionDescription())
			,self::REQUEST_VAR__ORDER_NUMBER => $this->orderIId()
			,self::REQUEST_VAR__SHOP_ID => $this->shopId()
			,self::REQUEST_VAR__URL_CONFIRM => $this->urlConfirm()
			,self::REQUEST_VAR__URL_RETURN_OK => rm_url_checkout_success()
			,self::REQUEST_VAR__URL_RETURN_NO => rm_url_checkout_fail()
		);
	}

	const REQUEST_VAR__CUSTOMER__EMAIL = 'LMI_PAYMER_EMAIL';
	const REQUEST_VAR__HTTP_METHOD__RETURN_OK = 'LMI_SUCCESS_METHOD';
	const REQUEST_VAR__HTTP_METHOD__RETURN_NO = 'LMI_FAIL_METHOD';
	const REQUEST_VAR__ORDER_AMOUNT = 'LMI_PAYMENT_AMOUNT';
	const REQUEST_VAR__ORDER_COMMENT = 'LMI_PAYMENT_DESC_BASE64';
	const REQUEST_VAR__ORDER_NUMBER = 'LMI_PAYMENT_NO';
	const REQUEST_VAR__SHOP_ID = 'LMI_PAYEE_PURSE';
	const REQUEST_VAR__URL_CONFIRM = 'LMI_RESULT_URL';
	const REQUEST_VAR__URL_RETURN_OK = 'LMI_SUCCESS_URL';
	const REQUEST_VAR__URL_RETURN_NO = 'LMI_FAIL_URL';
	const REQUEST_VALUE__HTTP_METHOD_POST = 'POST';
}