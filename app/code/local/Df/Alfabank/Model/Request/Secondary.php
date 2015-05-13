<?php
/**
 * @method Df_Alfabank_Model_Payment getPaymentMethod()
 * @method Df_Alfabank_Model_Response getResponse()
 * @method Df_Alfabank_Model_Config_Area_Service getServiceConfig()
 */
abstract class Df_Alfabank_Model_Request_Secondary extends Df_Payment_Model_Request_Secondary {
	/** @return string */
	abstract protected function getServiceName();

	/** @return array(string => string|int|float) */
	protected function getAdditionalParams() {return array();}

	/**
	 * @override
	 * @return array(string => string|int)
	 */
	public function getParams() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array_merge(
				array(
					'userName' => $this->getServiceConfig()->getShopId()
					,'password' => $this->getServiceConfig()->getRequestPassword()
					,'orderId' => $this->getPaymentExternalId()
				)
				,$this->getAdditionalParams()
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return Zend_Uri_Http
	 */
	public function getUri() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getServiceConfig()->getUri($this->getServiceName());
			$this->{__METHOD__}->setQuery($this->getParams());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getPaymentExternalId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->getOrderPayment()->getAdditionalInformation(
					Df_Alfabank_Model_Payment::INFO__PAYMENT_EXTERNAL_ID
				)
			;
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getResponseAsArray() {
		if (!isset($this->{__METHOD__})) {
			try {
				$this->{__METHOD__} = Zend_Json::decode($this->getResponseAsJson());
			}
			catch (Zend_Json_Exception $e) {
				df_notify_exception($e);
				df_error($this->getGenericFailureMessage());
			}
			df_result_array($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getResponseAsJson() {
		if (!isset($this->{__METHOD__})) {
			/** @var Zend_Http_Client $httpClient */
			$httpClient = new Zend_Http_Client();
			$httpClient
				->setHeaders(array())
				->setUri($this->getUri())
				->setConfig(array('timeout' => 10))
			;
			/** @var Zend_Http_Response $response */
			$response = $httpClient->request(Zend_Http_Client::GET);
			/** @var string $responseAsJson */
			$this->{__METHOD__} = $response->getBody();
			df_assert($this->{__METHOD__}, $this->getGenericFailureMessage());
		}
		return $this->{__METHOD__};
	}
}