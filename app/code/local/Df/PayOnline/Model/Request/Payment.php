<?php
/**
 * @method Df_PayOnline_Model_Payment getMethod()
 * @method Df_PayOnline_Model_Config_Area_Service configS()
 */
class Df_PayOnline_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/**
	 * @override
	 * @return string
	 */
	 public function getTransactionDescription() {
		return preg_replace(
			'#[^a-zA-Zа-яА-Я0-9 \,\!\?\;\:\%\*\(\)-]#u', '', parent::getTransactionDescription()
		);
	}

	/**
	 * @override
	 * @see Df_Payment_Model_Request_Payment::_params()
	 * @used-by Df_Payment_Model_Request_Payment::params()
	 * @return array(string => string|int)
	 */
	protected function _params() {
		return array(
			self::REQUEST_VAR__CUSTOMER__ADDRESS => $this->street()
			,self::REQUEST_VAR__CUSTOMER__CITY => $this->city()
			,self::REQUEST_VAR__CUSTOMER__COUNTRY => $this->iso3()
			,self::REQUEST_VAR__CUSTOMER__EMAIL => $this->email()
			,self::REQUEST_VAR__CUSTOMER__ZIP => $this->postCode()
			,self::REQUEST_VAR__ENCODING => 'utf-8'
			,self::REQUEST_VAR__ORDER_AMOUNT => $this->amountS()
			,self::REQUEST_VAR__ORDER_COMMENT => $this->getTransactionDescription()
			,self::REQUEST_VAR__ORDER_CURRENCY => $this->configS()->getCurrencyCodeInServiceFormat()
			,self::REQUEST_VAR__ORDER_NUMBER => $this->orderIId()
			,self::REQUEST_VAR__SIGNATURE => $this->getSignature()
			,self::REQUEST_VAR__SHOP_ID => $this->shopId()
			,self::REQUEST_VAR__URL_RETURN_OK => rm_url_checkout_success()
			,self::REQUEST_VAR__URL_RETURN_NO => rm_url_checkout_fail()
		);
	}

	/** @return string */
	private function getSignature() {
		/** @var array(string => string) $params */
		$params = array(
			self::REQUEST_VAR__SHOP_ID => $this->shopId()
			,self::REQUEST_VAR__ORDER_NUMBER => $this->orderIId()
			,self::REQUEST_VAR__ORDER_AMOUNT  => $this->amountS()
			,self::REQUEST_VAR__ORDER_CURRENCY => $this->configS()->getCurrencyCodeInServiceFormat()
			,self::REQUEST_VAR__ORDER_COMMENT => $this->getTransactionDescription()
			,self::SIGNATURE_PARAM__PRIVATE_SECURITY_KEY => $this->password()
		);
		return strtolower(md5(implode(
			Df_PayOnline_Helper_Data::SIGNATURE_PARTS_SEPARATOR
			,df_h()->payOnline()->preprocessSignatureParams(
				$this->preprocessParams($params)
			)
		)));
	}

	const REQUEST_VAR__CUSTOMER__ADDRESS = 'Address';
	const REQUEST_VAR__CUSTOMER__CITY = 'City';
	const REQUEST_VAR__CUSTOMER__COUNTRY = 'Country';
	const REQUEST_VAR__CUSTOMER__EMAIL = 'Email';
	const REQUEST_VAR__CUSTOMER__PHONE = 'Phone';
	const REQUEST_VAR__CUSTOMER__ZIP = 'Zip';
	const REQUEST_VAR__ENCODING = 'Encoding';
	const REQUEST_VAR__ORDER_AMOUNT = 'Amount';
	const REQUEST_VAR__ORDER_COMMENT = 'OrderDescription';
	const REQUEST_VAR__ORDER_CURRENCY = 'Currency';
	const REQUEST_VAR__ORDER_NUMBER = 'OrderId';
	const REQUEST_VAR__SIGNATURE = 'SecurityKey';
	const REQUEST_VAR__SHOP_ID = 'MerchantId';
	const REQUEST_VAR__URL_RETURN_OK = 'ReturnUrl';
	const REQUEST_VAR__URL_RETURN_NO = 'FailUrl';
	const SIGNATURE_PARAM__PRIVATE_SECURITY_KEY = 'PrivateSecurityKey';
}