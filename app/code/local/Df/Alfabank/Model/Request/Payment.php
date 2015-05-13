<?php
/**
 * @method Df_Alfabank_Model_Config_Area_Service getServiceConfig()
 */
class Df_Alfabank_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/** @return string */
	public function getPaymentPageUrl() {return df_a($this->getResponseAsArray(), 'formUrl');}

	/**
	 * @override
	 * @return array(string => string|int)
	 */
	protected function getParamsInternal() {
		/** @var array(string => string|int) $result */
		$result =
			array(
				'amount' => rm_round(100 * $this->getAmount()->getAsFixedFloat())
				,'currency' => 810
				,'orderNumber' => $this->getOrder()->getIncrementId()
				,'password' => $this->getServiceConfig()->getRequestPassword()
				,'returnUrl' => $this->getCustomerReturnUrl()
				,'userName' => $this->getServiceConfig()->getShopId()
			)
		;
		return $result;
	}

	/** @return string */
	private function getGenericErrorMessage() {
		return
			'Платёжный шлюз Альфа-Банка в настоящее время не отвечает.'
			.'<br/>Пожалуйста, выберите другой способ оплаты или оформите заказ по телефону.'
		;
	}

	/** @return array(string => string) */
	private function getResponseAsArray() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => string) $result */
			$result = null;
			try {
				$result = Zend_Json::decode($this->getResponseAsJson());
			}
			catch (Zend_Json_Exception $e) {
				$this->getPaymentMethod()
					->logFailureHighLevel(
						'Платёжный шлюз при регистрации заказа в системе вернул недопустимый ответ: «%s».'
						,$this->getResponseAsJson()
					)
				;
				$this->getPaymentMethod()->logFailureLowLevel($e);
				df_error($this->getGenericErrorMessage());
			}
			df_result_array($result);
			/** @var int $errorCode */
			$errorCode = rm_int(df_a($result, 'errorCode'));
			if (0 !== $errorCode) {
				df_error(df_a($result, 'errorMessage', $this->getGenericErrorMessage()));
			}
			$this->getPaymentMethod()->getInfoInstance()
				->setAdditionalInformation(
					Df_Alfabank_Model_Payment::INFO__PAYMENT_EXTERNAL_ID
					,df_a($result, 'orderId')
				)
				->save()
			;
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getResponseAsJson() {
		if (!isset($this->{__METHOD__})) {
			/** @var Zend_Uri_Http $uri */
			$uri = $this->getServiceConfig()->getUriPayment();
			$uri->setQuery($this->getParams());
			/** @var Zend_Http_Client $httpClient */
			$httpClient = new Zend_Http_Client();
			$httpClient
				->setHeaders(array())
				->setUri($uri)
				->setConfig(array('timeout' => 10))
			;
			/** @var Zend_Http_Response $response */
			$response = $httpClient->request(Zend_Http_Client::GET);
			/** @var string $responseAsJson */
			$this->{__METHOD__} = $response->getBody();
			if (!$this->{__METHOD__}) {
				df_error($this->getGenericErrorMessage());
			}
		}
		return $this->{__METHOD__};
	}
}


