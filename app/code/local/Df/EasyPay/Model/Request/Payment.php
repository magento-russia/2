<?php
/** @method Df_EasyPay_Model_Payment getMethod() */
class Df_EasyPay_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/**
	 * @override
	 * @see Df_Payment_Model_Request_Payment::_params()
	 * @used-by Df_Payment_Model_Request_Payment::params()
	 * @return array(string => string|int)
	 */
	protected function _params() {
		return array(
			self::REQUEST_VAR__SHOP_ID => $this->shopId()
			,self::REQUEST_VAR__ORDER_NUMBER => $this->orderIId()
			// EASYPAY требует, чтобы суммы были целыми числами
			,self::REQUEST_VAR__ORDER_AMOUNT => $this->amount()->getAsInteger()
			,'EP_Expires' => 3
			,self::REQUEST_VAR__ORDER_COMMENT => $this->getTransactionDescription()
			,'EP_OrderInfo' => $this->getTransactionDescription()
			,self::REQUEST_VAR__SIGNATURE => $this->getSignature()
			,self::REQUEST_VAR__URL_RETURN_OK => df_url_checkout_success()
			,self::REQUEST_VAR__URL_RETURN_NO => df_url_checkout_fail()
			,'EP_URL_Type' => 'link'
			,self::REQUEST_VAR__REQUEST__TEST_MODE => df_01($this->configS()->isTestMode())
			,'EP_Encoding' => 'utf-8'
		);
	}

	/** @return string */
	private function getSignature() {
		/** @var string $result */
		$result = md5(implode($this->preprocessParams(array(
			self::REQUEST_VAR__SHOP_ID => $this->shopId()
			,'Encryption Key' => $this->password()
			,self::REQUEST_VAR__ORDER_NUMBER => $this->orderIId()
			,self::REQUEST_VAR__ORDER_AMOUNT => $this->amount()->getAsInteger()
		))));
		return $result;
	}

	const REQUEST_VAR__ORDER_AMOUNT = 'EP_Sum';
	const REQUEST_VAR__ORDER_COMMENT = 'EP_Comment';
	const REQUEST_VAR__ORDER_NUMBER = 'EP_OrderNo';
	const REQUEST_VAR__REQUEST__TEST_MODE = 'EP_Debug';
	const REQUEST_VAR__SHOP_ID = 'EP_MerNo';
	const REQUEST_VAR__SIGNATURE = 'EP_Hash';
	const REQUEST_VAR__URL_RETURN_NO = 'EP_Cancel_URL';
	const REQUEST_VAR__URL_RETURN_OK = 'EP_Success_URL';
}