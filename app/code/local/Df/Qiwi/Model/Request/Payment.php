<?php
/**
 * @method Df_Qiwi_Model_Payment getPaymentMethod()
 */
class Df_Qiwi_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/**
	 * @override
	 * @return array(string => string|int)
	 */
	protected function getParamsInternal() {
		/** @var array(string => string|int) $result */
		$result =
			array(
				self::REQUEST_VAR__CUSTOMER__PHONE => $this->getQiwiCustomerPhone()
				,self::REQUEST_VAR__ORDER_AMOUNT => $this->getAmount()->getAsString()
				,self::REQUEST_VAR__ORDER_CURRENCY =>
					$this->getServiceConfig()->getCurrencyCodeInServiceFormat()
				,self::REQUEST_VAR__ORDER_LIFETIME => 24 * 45
				,self::REQUEST_VAR__ORDER_NUMBER => $this->getOrder()->getIncrementId()
				,self::REQUEST_VAR__ORDER_COMMENT => $this->getTransactionDescription()
				,self::REQUEST_VAR__SHOP_ID => $this->getServiceConfig()->getShopId()
				,self::REQUEST_VAR__CUSTOMER__REGISTERED_ONLY => 0
			)
		;
		return $result;
	}

	/** @return string */
	private function getQiwiCustomerPhone() {
		/** @var string $result */
		$result = $this->getPaymentMethod()->getQiwiCustomerPhone();
		df_assert_eq(10, strlen($result));
		df_result_string($result);
		return $result;
	}

	const REQUEST_VAR__CUSTOMER__PHONE = 'to';
	const REQUEST_VAR__ORDER_AMOUNT = 'summ';
	const REQUEST_VAR__ORDER_COMMENT = 'com';
	const REQUEST_VAR__ORDER_CURRENCY = 'currency';
	const REQUEST_VAR__ORDER_LIFETIME = 'lifetime';
	const REQUEST_VAR__ORDER_NUMBER = 'txn_id';
	const REQUEST_VAR__CUSTOMER__REGISTERED_ONLY = 'check_agt';
	const REQUEST_VAR__SHOP_ID = 'from';
}