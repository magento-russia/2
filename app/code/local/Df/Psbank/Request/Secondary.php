<?php
/**
 * @method Df_Psbank_Method method()
 * @method Df_Psbank_Config_Area_Service configS()
 */
abstract class Df_Psbank_Request_Secondary extends Df_Payment_Request_Transaction {
	/** @return int */
	abstract protected function getTransactionType();

	/**
	 * @override
	 * @see Df_Payment_Request_Secondary::_params()
	 * @used-by Df_Payment_Request_Secondary::params()
	 * @return array(string => string|int)
	 */
	public function _params() {
		return array('P_SIGN' => $this->getSignature()) + $this->paramsForSignature();
	}

	/**
	 * @override
	 * @return Zend_Uri_Http
	 */
	public function getUri() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Zend_Uri::factory($this->configS()->getUrlPaymentPage());
		}
		return $this->{__METHOD__};
	}

	/** @return void */
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
			->setUri($this->getUri())
			->setConfig(array(
				/**
				 * 2014-11-05
				 * По умолчанию при обращении к адресам по протоколу https
				 * Zend Framework использует низкоуровневый протокол SSL:
				 * http://framework.zend.com/manual/1.12/en/zend.http.client.adapters.html
				 *
				 * Однако, вроде бы Промсвязьбанк прекращает поддержку SSL с 2014-11-10:
				 * http://magento-forum.ru/topic/4803/
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
			->setParameterPost($this->params())
		;
		$httpClient->request();
	}

	/**
	 * @override
	 * @see Df_Payment_Request_Secondary::getPaymentExternalId()
	 * @return string
	 */
	protected function getPaymentExternalId() {
		return $this->getResponsePayment()->getOperationExternalId();
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getResponseAsArray() {df_abstract($this); return null;}
	
	/** @return Df_Psbank_Response */
	private function getResponsePayment() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Psbank_Response::i(
				Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH
			)->loadFromPaymentInfo($this->ii());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getSignature() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Psbank_Helper_Data::s()->generateSignature(
					$this->paramsForSignature()
					,array(
						'ORDER', 'AMOUNT', 'CURRENCY', 'ORG_AMOUNT', 'RRN', 'INT_REF', 'TRTYPE'
						, 'TERMINAL', 'BACKREF', 'EMAIL', 'TIMESTAMP', 'NONCE'
					)
					,$this->password()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => string) */
	private function paramsForSignature() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array(
				'AMOUNT' => $this->getResponsePayment()->amount()->getAsString()
				,'BACKREF' => ''
				,'CURRENCY' => 'RUB'
				,'EMAIL' => df_store_mail_address()
				,'INT_REF' => $this->getPaymentExternalId()
				,'NONCE' => Df_Psbank_Helper_Data::s()->generateNonce()
				,'ORDER' => $this->order()->getIncrementId()
				,'ORG_AMOUNT' => $this->getResponsePayment()->amount()->getAsString()
				,'RRN' => $this->getResponsePayment()->getRetrievalReferenceNumber()
				,'TERMINAL' => $this->configS()->getTerminalId()
				,'TIMESTAMP' => Df_Psbank_Helper_Data::s()->getTimestamp()
				,'TRTYPE' => $this->getTransactionType()
			);
		}
		return $this->{__METHOD__};
	}
}