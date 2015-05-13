<?php
/**
 * @method Df_EasyPay_Model_Payment getPaymentMethod()
 */
class Df_EasyPay_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getParamsInternal() {
		/** @var array(string => string) $result */
		$result =
			array_merge(
				array(
					self::REQUEST_VAR__SHOP_ID => $this->getServiceConfig()->getShopId()
					,self::REQUEST_VAR__ORDER_NUMBER => $this->getOrder()->getIncrementId()
					,self::REQUEST_VAR__ORDER_AMOUNT =>
						$this
							->getAmount()
							/**
							 * EASYPAY требует, чтобы суммы были целыми числами
							 */
							->getAsInteger()
					,'EP_Expires' => 3
					,self::REQUEST_VAR__ORDER_COMMENT => $this->getTransactionDescription()
					,'EP_OrderInfo' => $this->getTransactionDescription()
					,self::REQUEST_VAR__SIGNATURE =>	$this->getSignature()
					,self::REQUEST_VAR__URL_RETURN_OK =>	$this->getUrlCheckoutSuccess()
					,self::REQUEST_VAR__URL_RETURN_NO =>	$this->getUrlCheckoutFail()
					,'EP_URL_Type' => 'link'
					,self::REQUEST_VAR__REQUEST__TEST_MODE => rm_01($this->getServiceConfig()->isTestMode())
					,'EP_Encoding' => 'utf-8'
				)
			)
		;
		return $result;
	}

	/** @return string */
	private function getSignature() {
		/** @var string $result */
		$result =
			md5(
				implode(
					$this->preprocessParams(array(
						self::REQUEST_VAR__SHOP_ID => $this->getServiceConfig()->getShopId()
						,'Encryption Key' => $this->getServiceConfig()->getResponsePassword()
						,self::REQUEST_VAR__ORDER_NUMBER => $this->getOrder()->getIncrementId()
						,self::REQUEST_VAR__ORDER_AMOUNT => $this->getAmount()->getAsInteger()
					))
				)
			)
		;
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