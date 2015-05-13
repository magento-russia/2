<?php
/**
 * @method Df_Masterbank_Model_Payment getPaymentMethod()
 */
abstract class Df_Masterbank_Model_Request_Secondary extends Df_Payment_Model_Request_Secondary {
	/** @return string */
	abstract protected function getServiceName();

	/**
	 * @override
	 * @return array(string => string|int)
	 */
	public function getParams() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array(
				'AMOUNT' => $this->getAmount()->getAsString()
				,'ORDER' => $this->getOrder()->getIncrementId()
				,'RRN' => $this->getPaymentExternalId()
				,'INT_REF' => $this->getResponsePayment()->getOperationCodeExternal()
				,'TERMINAL' => $this->getServiceConfig()->getShopId()
				,'TIMESTAMP' => Df_Masterbank_Helper_Data::s()->getTimestamp()
				,'SIGN' => Df_Masterbank_Helper_Data::s()->getSignature($this)
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
			/** @var Zend_Uri_Http $result */
			$result = Zend_Uri::factory('https');
			$result->setHost('pay.masterbank.ru');
			$result->setPath(df_concat_url('/acquiring', $this->getServiceName()));
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Masterbank_Model_Request_Secondary */
	public function process() {
		/** @var Zend_Http_Client $httpClient */
		$httpClient = new Zend_Http_Client();
		$httpClient
			->setHeaders(array())
			->setUri($this->getUri())
			->setConfig(array('timeout' => 10))
			->setMethod(Zend_Http_Client::POST)
			->setParameterPost($this->getParams())
		;
		/** @var Zend_Http_Response $response */
		$httpClient->request();
		return $this;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getPaymentExternalId() {return $this->getResponsePayment()->getRequestExternalId();}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getResponseAsArray() {df_abstract(__METHOD__);}

	/**
	 * @override
	 * @return string
	 */
	protected function getResponseClass() {df_abstract(__METHOD__);}
	
	/** @return Df_Masterbank_Model_Response_Payment */
	private function getResponsePayment() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Masterbank_Model_Response_Payment::i()->loadFromPaymentInfo(
					$this->getPaymentInfoInstance()
				)
			;
		}
		return $this->{__METHOD__};
	}
}