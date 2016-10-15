<?php
class Df_Avangard_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/**
	 * @override
	 * @return string
	 */
	public function getTransactionType() {
		return Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT;
	}

	/**
	 * @override
	 * @see Df_Payment_Model_Request_Payment::_params()
	 * @used-by Df_Payment_Model_Request_Payment::params()
	 * @return array(string => string|int)
	 */
	protected function _params() {
		return array('ticket' => $this->getResponse()->getPaymentExternalId());
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
				->setUri($this->getRequestUri())
				->setConfig(array('timeout' => 3))
			;
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
			$this->{__METHOD__} = Df_Avangard_Model_RequestDocument::registration(array(
				'shop_id' => $this->shopId()
				,'shop_passwd' => $this->password()
				,'amount' => df_round(100 * $this->amount()->getAsFixedFloat())
				,'order_number' => $this->orderIId()
				,'order_description' => $this->getTransactionDescription()
				,'language' => 'RU'
				,'back_url' => $this->urlCustomerReturn()
				,'client_name' => $this->getCustomerNameFull()
				,'client_address' => $this->street()
				,'client_phone' => $this->phone()
				,'client_email' => $this->email()
				,'client_ip' => $this->getCustomerIpAddress()
			));
			df_report('registration-request-{date}-{time}.xml', $this->{__METHOD__}->getXml());
		}
		return $this->{__METHOD__};
	}
	
	/** @return Zend_Uri_Http */
	private function getRequestUri() {
		if (!isset($this->{__METHOD__})) {
			/** @var Zend_Uri_Http $result */
			$result = Zend_Uri::factory('https');
			$result->setHost('www.avangard.ru');
			$result->setPath('/iacq/h2h/reg');
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Avangard_Model_Response_Registration */
	private function getResponse() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Avangard_Model_Response_Registration::i(
				$this->getResponseAsSimpleXml()->asCanonicalArray()
			);
			$this->{__METHOD__}->postProcess($this->payment());
		}
		return $this->{__METHOD__};
	}

	/** @return \Df\Xml\X */
	private function getResponseAsSimpleXml() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_xml_parse($this->getHttpResponse()->getBody());
			df_report('registration-{date}-{time}.xml', $this->getHttpResponse()->getBody());
		}
		return $this->{__METHOD__};
	}
}