<?php
namespace Df\WebMoney\Request;
/**
 * @method \Df\WebMoney\Config\Area\Service configS()
 * @method \Df\WebMoney\Method method()
 */
class Payment extends \Df\Payment\Request\Payment {
	/**
	 * @override
	 * @see \Df\Payment\Request\Payment::_params()
	 * @used-by \Df\Payment\Request\Payment::params()
	 * @return array(string => string|int)
	 */
	protected function _params() {
		return array(
			self::REQUEST_VAR__CUSTOMER__EMAIL => $this->email()
			,self::REQUEST_VAR__HTTP_METHOD__RETURN_OK => self::REQUEST_VALUE__HTTP_METHOD_POST
			,self::REQUEST_VAR__HTTP_METHOD__RETURN_NO => self::REQUEST_VALUE__HTTP_METHOD_POST
			,self::REQUEST_VAR__ORDER_AMOUNT => $this->amountS()
			,self::REQUEST_VAR__ORDER_COMMENT => base64_encode($this->description())
			,self::REQUEST_VAR__ORDER_NUMBER => $this->orderIId()
			,self::REQUEST_VAR__SHOP_ID => $this->shopId()
			,self::REQUEST_VAR__URL_CONFIRM => $this->urlConfirm()
			,self::REQUEST_VAR__URL_RETURN_OK => df_url_checkout_success()
			,self::REQUEST_VAR__URL_RETURN_NO => df_url_checkout_fail()
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