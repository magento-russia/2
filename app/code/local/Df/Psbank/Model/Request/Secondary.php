<?php
/**
 * @method Df_Psbank_Model_Payment getPaymentMethod()
 * @method Df_Psbank_Model_Config_Area_Service getServiceConfig()
 */
abstract class Df_Psbank_Model_Request_Secondary extends Df_Payment_Model_Request_Secondary {
	/** @return int */
	abstract protected function getTransactionType();

	/**
	 * @override
	 * @return array(string => string|int)
	 */
	public function getParams() {
		return array_merge($this->getParamsForSignature(), array('P_SIGN' => $this->getSignature()));
	}

	/**
	 * @override
	 * @return Zend_Uri_Http
	 */
	public function getUri() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Zend_Uri::factory($this->getServiceConfig()->getUrlPaymentPage()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Psbank_Model_Request_Secondary */
	public function process() {
		/** @var Zend_Http_Client $httpClient */
		$httpClient = new Zend_Http_Client();
		if (!in_array('tls', stream_get_transports())) {
			df_error(
				'Для работы модуля Промсвязьбанк'
				. ' сервер интернет-магазина должен поддерживать протокол TLS.'
			);
		}
		$httpClient
			->setHeaders(array())
			->setUri($this->getUri())
			->setConfig(array(
				/**
				 * 2014-11-05
				 * По умолчанию при обращении к адресам по протоколу https
				 * Zend Framework использует низкоуровневый протокол SSL:
				 * @link http://framework.zend.com/manual/1.12/en/zend.http.client.adapters.html
				 *
				 * Однако, вроде бы Промсвязьбанк прекращает поддержку SSL с 2014-11-10:
				 * @link http://magento-forum.ru/topic/4803/
				 *
				 * Поэтому используем низкоуровневый протокол TLS
				 */
				'ssltransport' => 'tls'
				/**
				 * @see Zend_Http_Client_Adapter_Socket и так является адаптером по умолчанию,
				 * но примеры из документации всегда указывают этот параметр
				 * при указании нестандартного параметра ssltransport
				 * (видимо, потому что параметр ssltransport имеет смысл
				 * только для Zend_Http_Client_Adapter_Socket).
				 * Поэтому и мы указываем адаптер в явном виде.
				 */
				, 'adapter' => 'Zend_Http_Client_Adapter_Socket'
				, 'timeout' => 10
			))
			->setMethod(Zend_Http_Client::POST)
			->setParameterPost($this->getParams())
		;
		/** @var Zend_Http_Response $response */
		$response = $httpClient->request();
		return $this;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getPaymentExternalId() {
		return $this->getResponsePayment()->getOperationExternalId();
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

	/** @return array(string => string) */
	private function getParamsForSignature() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array(
				'AMOUNT' => $this->getResponsePayment()->getAmount()->getAsString()
				,'BACKREF' => ''
				,'CURRENCY' => 'RUB'
				,'EMAIL' => Df_Core_Helper_Mail::s()->getCurrentStoreMailAddress()
				,'INT_REF' => $this->getPaymentExternalId()
				,'NONCE' => Df_Psbank_Helper_Data::s()->generateNonce()
				,'ORDER' => $this->getOrder()->getIncrementId()
				,'ORG_AMOUNT' => $this->getResponsePayment()->getAmount()->getAsString()
				,'RRN' => $this->getResponsePayment()->getRetrievalReferenceNumber()
				,'TERMINAL' => $this->getServiceConfig()->getTerminalId()
				,'TIMESTAMP' => Df_Psbank_Helper_Data::s()->getTimestamp()
				,'TRTYPE' => $this->getTransactionType()
			);
		}
		return $this->{__METHOD__};
	}
	
	/** @return Df_Psbank_Model_Response */
	private function getResponsePayment() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Psbank_Model_Response::i(Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH)
					->loadFromPaymentInfo($this->getPaymentInfoInstance())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getSignature() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Psbank_Helper_Data::s()->generateSignature(
					$this->getParamsForSignature()
					,array(
						'ORDER', 'AMOUNT', 'CURRENCY', 'ORG_AMOUNT', 'RRN', 'INT_REF', 'TRTYPE'
						, 'TERMINAL', 'BACKREF', 'EMAIL', 'TIMESTAMP', 'NONCE'
					)
					,$this->getServiceConfig()->getRequestPassword()
				)
			;
		}
		return $this->{__METHOD__};
	}
}