<?php
/**
 * @method Df_Avangard_Model_Payment getPaymentMethod()
 */
abstract class Df_Avangard_Model_Request_Secondary extends Df_Payment_Model_Request_Secondary {
	/** @return string */
	abstract protected function getRequestDocumentTag();

	/** @return string */
	abstract protected function getRequestUriSuffix();

	/**
	 * @override
	 * @return array(string => string)
	 */
	public function getParams() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array(
				'shop_id' => $this->getServiceConfig()->getShopId()
				,'shop_passwd' => $this->getServiceConfig()->getRequestPassword()
				,'ticket' => $this->getPaymentExternalId()
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
			$result->setHost('www.avangard.ru');
			$result->setPath(rm_sprintf('/iacq/h2h/%s', $this->getRequestUriSuffix()));
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
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
				 * @link http://magento-forum.ru/topic/4100/
				 */
				->setRawData(
					http_build_query(array('xml' => $this->getRequestDocument()->getXml()), '', '&')
					,'application/x-www-form-urlencoded; charset=utf-8'
				)
				->setMethod(Zend_Http_Client::POST)
				->setUri($this->getUri())
				->setConfig(array('timeout' => 3))
			;
			df()->debug()->report(
				rm_sprintf('%s-request-{date}-{time}.xml', $this->getRequestUriSuffix())
				,$this->getRequestDocument()->getXml()
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
	
	/** @return Df_Avangard_Model_RequestDocument */
	private function getRequestDocument() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Avangard_Model_RequestDocument::i(
					$requestParams = $this->getParams()
					,$tagName = $this->getRequestDocumentTag()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Varien_Simplexml_Element */
	private function getResponseAsSimpleXml() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_xml($this->getHttpResponse()->getBody());
			df()->debug()->report(
				rm_sprintf('%s-response-{date}-{time}.xml', $this->getRequestUriSuffix())
				,$this->getHttpResponse()->getBody()
			);
		}
		return $this->{__METHOD__};
	}
	
	/** @return Df_Avangard_Model_Response_Registration */
	private function getResponseRegistration() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Avangard_Model_Response_Registration::i();
			$this->{__METHOD__}->loadFromPaymentInfo($this->getOrderPayment());
		}
		return $this->{__METHOD__};
	}
	
	const _CLASS = __CLASS__;
}