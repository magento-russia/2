<?php
/**
 * @method Df_YandexMoney_Model_Config_Area_Service getServiceConfig()
 */
class Df_YandexMoney_Model_Request_Capture extends Df_YandexMoney_Model_Request_Secondary {
	/**
	 * @override
	 * @return string
	 */
	protected function getGenericFailureMessageUniquePart() {
		return 'проведении платежа в системе Яндекс.Деньги';
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getParamsUnique() {
		return array(
			'request_id' => $this->getPaymentExternalId()
			,'money_source' => 'wallet'
		);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getPaymentExternalId() {
		return $this->getResponseAuthorize()->getOperationExternalId();
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getRequestType() {return 'process-payment';}

	/**
	 * @override
	 * @return string
	 */
	protected function getResponseClass() {return Df_YandexMoney_Model_Response_Capture::_CLASS;}

	/** @return Df_YandexMoney_Model_Response_Authorize */
	private function getResponseAuthorize() {return $this->cfg(self::P__RESPONSE_AUTHORIZE);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__RESPONSE_AUTHORIZE, Df_YandexMoney_Model_Response_Authorize::_CLASS);
	}
	const P__RESPONSE_AUTHORIZE = 'response_authorize';
	/**
	 * @param Df_YandexMoney_Model_Payment $paymentMethod
	 * @param Mage_Sales_Model_Order_Payment $orderPayment
	 * @param Df_YandexMoney_Model_Response_Authorize $responseAuthorize
	 * @param string $token
	 * @return Df_YandexMoney_Model_Request_Capture
	 */
	public static function i(
		Df_YandexMoney_Model_Payment $paymentMethod
		, Mage_Sales_Model_Order_Payment $orderPayment
		, Df_YandexMoney_Model_Response_Authorize $responseAuthorize
		, $token
	) {
		return new self(array(
			self::P__PAYMENT_METHOD => $paymentMethod
			, self::P__ORDER_PAYMENT => $orderPayment
			, self::P__RESPONSE_AUTHORIZE => $responseAuthorize
			, self::P__TOKEN => $token
		));
	}
}