<?php
/**
 * @method Df_WebMoney_Model_Config_Area_Service getServiceConfig()
 * @method Df_WebMoney_Model_Payment getPaymentMethod()
 */
class Df_WebMoney_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getParamsInternal() {
		return
			array(
				self::REQUEST_VAR__CUSTOMER__EMAIL => $this->getCustomerEmail()
				,self::REQUEST_VAR__HTTP_METHOD__RETURN_OK => self::REQUEST_VALUE__HTTP_METHOD_POST
				,self::REQUEST_VAR__HTTP_METHOD__RETURN_NO => self::REQUEST_VALUE__HTTP_METHOD_POST
				,self::REQUEST_VAR__ORDER_AMOUNT => $this->getAmount()->getAsString()
				,self::REQUEST_VAR__ORDER_COMMENT => base64_encode($this->getTransactionDescription())
				,self::REQUEST_VAR__ORDER_NUMBER => $this->getOrder()->getIncrementId()
				,self::REQUEST_VAR__SHOP_ID => $this->getServiceConfig()->getShopId()
				,self::REQUEST_VAR__URL_CONFIRM => $this->getUrlConfirm()
				,self::REQUEST_VAR__URL_RETURN_OK => $this->getUrlCheckoutSuccess()
				,self::REQUEST_VAR__URL_RETURN_NO => $this->getUrlCheckoutFail()
			)
		;
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