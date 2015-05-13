<?php
/**
 * @method Df_OnPay_Model_Payment getPaymentMethod()
 * @method Df_OnPay_Model_Config_Area_Service getServiceConfig()
 */
class Df_OnPay_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getParamsInternal() {
		/** @var array(string => string) $result */
		$result =
			array(
				self::REQUEST_VAR__CUSTOMER__EMAIL => $this->getCustomerEmail()
				,self::REQUEST_VAR__ORDER_AMOUNT =>
					df_h()->onPay()->priceToString($this->getAmount())
				,self::REQUEST_VAR__ORDER_COMMENT => $this->getTransactionDescription()
				,self::REQUEST_VAR__ORDER_CURRENCY =>
					$this->getServiceConfig()->getCurrencyCodeInServiceFormat()
				,self::REQUEST_VAR__ORDER_NUMBER => $this->getOrder()->getIncrementId()
				,self::REQUEST_VAR__PAYMENT_SERVICE__IS_FEE_PAYED_BY_SHOP =>
					rm_bts(
							Df_Payment_Model_Config_Source_Service_FeePayer::VALUE__SHOP
						===
							$this->getServiceConfig()->getFeePayer()
					)
				,self::REQUEST_VAR__PAYMENT_SERVICE__LANGUAGE =>
					$this->getServiceConfig()->getLocaleCodeInServiceFormat()
				,self::REQUEST_VAR__PAYMENT_SERVICE__NEED_CONVERT_RECEIPTS =>
					$this->needConvertReceipts()
				,self::REQUEST_VAR__PAYMENT_SERVICE__PAYMENT_MODE => self::PAYMENT_MODE__FIX
				,self::REQUEST_VAR__SIGNATURE => $this->getSignature()
				,self::REQUEST_VAR__URL_RETURN_OK => $this->getUrlCheckoutSuccess()
				,self::REQUEST_VAR__URL_RETURN_NO => $this->getUrlCheckoutFail()
			)
		;
		return $result;
	}

	/** @return string */
	private function getSignature() {
		return
			df_h()->onPay()->generateSignature(
				$this->preprocessParams(
					array(
						self::REQUEST_VAR__PAYMENT_SERVICE__PAYMENT_MODE=> self::PAYMENT_MODE__FIX
						,self::REQUEST_VAR__ORDER_AMOUNT =>
							df_h()->onPay()->priceToString($this->getAmount())
						,self::REQUEST_VAR__ORDER_CURRENCY =>
							$this->getServiceConfig()->getCurrencyCodeInServiceFormat()
						,self::REQUEST_VAR__ORDER_NUMBER => $this->getOrder()->getIncrementId()
						,self::REQUEST_VAR__PAYMENT_SERVICE__NEED_CONVERT_RECEIPTS =>
							$this->needConvertReceipts()
						,self::SIGNATURE_PARAM__ENCRYPTION_KEY =>
							$this->getServiceConfig()->getResponsePassword()
					)
				)
			)
		;
	}

	/** @return string */
	private function needConvertReceipts() {
		return
			(
					Df_OnPay_Model_Config_Source_Service_ReceiptCurrency::VALUE__BILL
				===
					$this->getServiceConfig()->getReceiptCurrency()
			)
			? 'yes'
			: 'no'
		;
	}

	const PAYMENT_MODE__FIX = 'fix';
	const REQUEST_VAR__CUSTOMER__EMAIL = 'user_email';
	const REQUEST_VAR__ORDER_AMOUNT = 'price';
	const REQUEST_VAR__ORDER_COMMENT = 'note';
	const REQUEST_VAR__ORDER_CURRENCY = 'currency';
	const REQUEST_VAR__ORDER_NUMBER = 'pay_for';
	const REQUEST_VAR__PAYMENT_SERVICE__IS_FEE_PAYED_BY_SHOP = 'price_final';
	const REQUEST_VAR__PAYMENT_SERVICE__LANGUAGE = 'ln';
	const REQUEST_VAR__PAYMENT_SERVICE__NEED_CONVERT_RECEIPTS = 'convert';
	const REQUEST_VAR__PAYMENT_SERVICE__PAYMENT_MODE = 'pay_mode';
	const REQUEST_VAR__SIGNATURE = 'md5';
	const REQUEST_VAR__URL_RETURN_OK = 'url_success';
	const REQUEST_VAR__URL_RETURN_NO = 'url_fail';
	const SIGNATURE_PARAM__ENCRYPTION_KEY = 'encryption_key';
}