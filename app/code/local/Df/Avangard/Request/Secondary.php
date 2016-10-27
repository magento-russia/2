<?php
/** @method Df_Avangard_Method method() */
abstract class Df_Avangard_Request_Secondary extends \Df\Payment\Request\Transaction {
	/** @return string */
	abstract protected function getRequestId();

	/**
	 * @override
	 * @return Zend_Uri_Http
	 */
	public function getUri() {
		if (!isset($this->{__METHOD__})) {
			/** @var Zend_Uri_Http $result */
			$result = Zend_Uri::factory('https');
			$result->setHost('www.avangard.ru');
			$result->setPath('/iacq/h2h/' . $this->getRequestId());
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @see \Df\Payment\Request\Secondary::_params()
	 * @used-by \Df\Payment\Request\Secondary::params()
	 * @return array(string => string|int)
	 */
	protected function _params() {
		return array(
			'shop_id' => $this->shopId()
			,'shop_passwd' => $this->password()
			,'ticket' => $this->getPaymentExternalId()
		);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getPaymentExternalId() {
		return $this->getResponseRegistration()->getPaymentExternalId();
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getResponseAsArray() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getResponseAsSimpleXml()->asCanonicalArray();
		}
		return $this->{__METHOD__};
	}

	/** @return Zend_Http_Client */
	private function getHttpClient() {
		if (!isset($this->{__METHOD__})) {
			/** @var Zend_Http_Client $result */
			$result = new Zend_Http_Client();
			$result
				/**
				 * Чтобы внутренняя информационная система банка Авангард
				 * обработала наш запрос в правильной кодировке,
				 * пробуем явно указать кодировку содержимого
				 * заданием значения «%application/x-www-form-urlencoded; charset=utf-8»
				 * для заголовка HTTP «Content-Type» вместо автоматического значения
				 * «%application/x-www-form-urlencoded».
				 * Обратите внимание, что это нельзя сделать посредством
				 * Zend_Http_Client::setHeaders или Zend_Http_Client::setEncType,
				 * потому что иначе Zend_Http_Client возбудит исключительную ситуацию
				 * «Cannot handle content type
				 * 'application/x-www-form-urlencoded; charset=utf-8' automatically.
				 * Please use Zend_Http_Client::setRawData to send this kind of content.»
				 * @see Zend_Http_Client::_prepareBody()
				 * http://magento-forum.ru/topic/4100/
				 */
				->setRawData(
					http_build_query(array('xml' => $this->getRequestDocument()->getXml()), '', '&')
					,'application/x-www-form-urlencoded; charset=utf-8'
				)
				->setMethod(Zend_Http_Client::POST)
				->setUri($this->getUri())
				->setConfig(array('timeout' => 3))
			;
			df_report(
				$this->getRequestId() . '-request-{date}-{time}.xml'
				, $this->getRequestDocument()->getXml()
			);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Zend_Http_Response */
	private function getHttpResponse() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getHttpClient()->request();
		}
		return $this->{__METHOD__};
	}
	
	/** @return Df_Avangard_RequestDocument */
	private function getRequestDocument() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Avangard_RequestDocument::i(
				$this->params(), $this->getRequestId()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return \Df\Xml\X */
	private function getResponseAsSimpleXml() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_xml_parse($this->getHttpResponse()->getBody());
			df_report(
				$this->getRequestId() . '-response-{date}-{time}.xml'
				,$this->getHttpResponse()->getBody()
			);
		}
		return $this->{__METHOD__};
	}
	
	/** @return Df_Avangard_Response_Registration */
	private function getResponseRegistration() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Avangard_Response_Registration::i();
			$this->{__METHOD__}->loadFromPaymentInfo($this->payment());
		}
		return $this->{__METHOD__};
	}
}