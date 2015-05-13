<?php
/**
 * @method Df_Kkb_Model_Payment getPaymentMethod()
 */
abstract class Df_Kkb_Model_Request_Secondary extends Df_Payment_Model_Request_Secondary {
	/** @return string */
	abstract public function getTransactionType();

	/**
	 * Используется только для диагностики!
	 * @see Df_Payment_Model_Request_Secondary::getParams()
	 * @override
	 * @return array(string => string)
	 */
	public function getParams() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array('document' => $this->getRequestDocument()->getXml());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	public function getPaymentExternalId() {return $this->getResponsePayment()->getPaymentId();}
	
	/**
	 * @override
	 * @return Df_Kkb_Model_Response_Secondary
	 */
	public function getResponse() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Kkb_Model_Response_Secondary::i($this->getResponseAsXml());
			$this->{__METHOD__}->postProcess($this->getOrderPayment());
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Kkb_Model_Response_Payment */
	public function getResponsePayment() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Kkb_Model_Response_Payment::i()->loadFromPaymentInfo($this->getOrderPayment())
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return Zend_Uri_Http
	 */
	public function getUri() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Zend_Uri::factory(
				strtr('https://{host}/jsp/remote/control.jsp?{document}', array(
					'{host}' => $this->getHost()
					,'{document}' => rawurlencode($this->getRequestDocument()->getXml())
				)
			));
		}
		return $this->{__METHOD__};
	}

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

	/** @return string */
	private function getHost() {
		return $this->getPaymentMethod()->isTestMode() ? '3dsecure.kkb.kz' : 'epay.kkb.kz';
	}
	
	/** @return Df_Kkb_Model_RequestDocument_Secondary */
	private function getRequestDocument() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Kkb_Model_RequestDocument_Secondary::i($this);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getResponseAsXml() {
		if (!isset($this->{__METHOD__})) {
			/** @var Zend_Http_Client $httpClient */
			$httpClient = new Zend_Http_Client();
			$httpClient
				->setHeaders(array())
				->setUri($this->getUri())
				->setConfig(array('timeout' => 10))
				->setMethod(Zend_Http_Client::GET)
			;
			/** @var Zend_Http_Response $response */
			$this->{__METHOD__} = df_trim($httpClient->request()->getBody());
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}
}