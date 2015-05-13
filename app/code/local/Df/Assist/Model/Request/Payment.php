<?php
/**
 * @method Df_Assist_Model_Payment getPaymentMethod()
 */
class Df_Assist_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/**
	 * @override
	 * @return string[]
	 */
	protected function getParamsInternal() {
		/** @var Mage_Directory_Model_Region $region */
		$region = $this->getBillingAddress()->getRegionModel();
		/** @var string[] $result */
		$result =
			array_merge(
				array(
					self::REQUEST_VAR__CUSTOMER__ADDRESS => $this->getAddressStreet()
					,self::REQUEST_VAR__CUSTOMER__CITY => $this->getBillingAddress()->getCity()
					,self::REQUEST_VAR__CUSTOMER__COUNTRY =>
						$this->getBillingAddress()->getCountryModel()->getIso3Code()
					,self::REQUEST_VAR__CUSTOMER__EMAIL => $this->getCustomerEmail()
					,self::REQUEST_VAR__CUSTOMER__NAME_FIRST =>
						$this->getOrder()->getCustomerFirstname()
					,self::REQUEST_VAR__CUSTOMER__NAME_LAST =>
						$this->getOrder()->getCustomerLastname()
					,self::REQUEST_VAR__CUSTOMER__NAME_MIDDLE =>
						$this->getOrder()->getCustomerMiddlename()
					,self::REQUEST_VAR__CUSTOMER__PHONE_HOME => $this->getCustomerPhone()
					,self::REQUEST_VAR__CUSTOMER__STATE => $region->getCode()
					,self::REQUEST_VAR__CUSTOMER__ZIP =>
						$this->getBillingAddress()->getPostcode()
					,self::REQUEST_VAR__ORDER_AMOUNT => $this->getAmount()->getAsString()
					,self::REQUEST_VAR__ORDER_CURRENCY =>
						$this->getServiceConfig()->getCurrencyCodeInServiceFormat()
					,self::REQUEST_VAR__ORDER_NUMBER => $this->getOrder()->getIncrementId()
					,self::REQUEST_VAR__PAYMENT_SERVICE__DELAY =>
						rm_01($this->getServiceConfig()->isCardPaymentActionAuthorize())
					,self::REQUEST_VAR__PAYMENT_SERVICE__LANGUAGE =>
						$this->getServiceConfig()->getLocaleCodeInServiceFormat()
					,self::REQUEST_VAR__REQUEST__TEST_MODE =>
						rm_01($this->getPaymentMethod()->isTestMode())
					,self::REQUEST_VAR__RECURRING_INDICATOR => 0
					,self::REQUEST_VAR__SHOP_ID => $this->getServiceConfig()->getShopId()
					,self::REQUEST_VAR__URL_RETURN_OK => $this->getUrlCheckoutSuccess()
					,self::REQUEST_VAR__URL_RETURN_NO => $this->getUrlCheckoutFail()
				)
				,$this->getPaymentSystemMethods()
			)
		;
		return $result;
	}

	/** @return int[] */
	private function getPaymentSystemMethods() {
		if (!isset($this->{__METHOD__})) {
			/** @var int[] $result */
			$result = array();
			foreach ($this->getServiceConfig()->getDisabledPaymentMethods() as $methodCode) {
				/** @var string $methodCode */
				df_assert_string_not_empty($methodCode);
				$result[$methodCode] = 0;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	const REQUEST_VAR__CUSTOMER__ADDRESS = 'Address';
	const REQUEST_VAR__CUSTOMER__CITY = 'City';
	const REQUEST_VAR__CUSTOMER__COUNTRY = 'Country';
	const REQUEST_VAR__CUSTOMER__EMAIL = 'Email';
	const REQUEST_VAR__CUSTOMER__FAX = 'Fax';
	const REQUEST_VAR__CUSTOMER__NAME_FIRST = 'Firstname';
	const REQUEST_VAR__CUSTOMER__NAME_LAST = 'Lastname';
	const REQUEST_VAR__CUSTOMER__NAME_MIDDLE = 'Middlename';
	const REQUEST_VAR__CUSTOMER__PHONE_HOME = 'HomePhone';
	const REQUEST_VAR__CUSTOMER__PHONE_MOBILE = 'MobilePhone';
	const REQUEST_VAR__CUSTOMER__PHONE_WORK = 'WorkPhone';
	const REQUEST_VAR__CUSTOMER__STATE = 'State';
	const REQUEST_VAR__CUSTOMER__ZIP = 'Zip';
	const REQUEST_VAR__ORDER_AMOUNT = 'OrderAmount';
	const REQUEST_VAR__ORDER_COMMENT = 'OrderComment';
	const REQUEST_VAR__ORDER_CURRENCY = 'OrderCurrency';
	const REQUEST_VAR__ORDER_NUMBER = 'OrderNumber';
	const REQUEST_VAR__PAYMENT_METHOD__CARD = 'CardPayment';
	const REQUEST_VAR__PAYMENT_METHOD__QIWI = 'QIWIPayment';
	const REQUEST_VAR__PAYMENT_METHOD__QIWI_BEELINE = 'QIWIBeelinePayment';
	const REQUEST_VAR__PAYMENT_METHOD__QIWI_MEGAFON = 'QIWIMegafonPayment';
	const REQUEST_VAR__PAYMENT_METHOD__QIWI_MTS = 'QIWIMtsPayment';
	const REQUEST_VAR__PAYMENT_METHOD__WM = 'WMPayment';
	const REQUEST_VAR__PAYMENT_METHOD__YM = 'YMPayment';
	const REQUEST_VAR__PAYMENT_SERVICE__DELAY = 'Delay';
	const REQUEST_VAR__PAYMENT_SERVICE__LANGUAGE = 'Language';
	const REQUEST_VAR__PAYMENT_SERVICE__USE_ASSIST_ID = 'AssistIDPayment';
	const REQUEST_VAR__REQUEST__SIGNATURE = 'Signature';
	const REQUEST_VAR__REQUEST__TEST_MODE = 'TestMode';
	const REQUEST_VAR__RECURRING_INDICATOR = 'RecurringIndicator';
	const REQUEST_VAR__RECURRING_MIN_AMOUNT = 'RecurringMinAmount';
	const REQUEST_VAR__RECURRING_MAX_AMOUNT = 'RecurringMaxAmount';
	const REQUEST_VAR__RECURRING_MAX_DATE = 'RecurringMaxDate';
	const REQUEST_VAR__RECURRING_PERIOD = 'RecurringPeriod';
	const REQUEST_VAR__SHOP_ID = 'Merchant_ID';
	const REQUEST_VAR__URL_RETURN = 'URL_RETURN';
	const REQUEST_VAR__URL_RETURN_OK = 'URL_RETURN_OK';
	const REQUEST_VAR__URL_RETURN_NO = 'URL_RETURN_NO';
}