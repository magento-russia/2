<?php
class Df_Sms_Model_Gate_Sms16Ru extends Df_Sms_Model_Gate {
	/** @return Df_Sms_Model_Gate_Sms16Ru */
	public function send() {
		/** @var Zend_Http_Client $httpClient */
		$httpClient = new Zend_Http_Client();
		$httpClient->setRawData($this->getRequestBody(), 'text/xml');
		$httpClient
			->setHeaders(
				array(
					'Accept' => 'test/xml'
				)
			)
			->setUri('http://xml.sms16.ru/xml/')
			->setConfig(
				array(
					'timeout' => 10
				)
			)
		;
		$httpClient->setMethod(Zend_Http_Client::POST);
		/** @var Zend_Http_Response $response */
		$response = $httpClient->request();
		/** @var string $responseAsXml */
		$responseAsXml = $response->getBody();
		Mage::log($responseAsXml);
		return $this;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getSenderName() {
		return
			/**
			 * Намеренно используем substr, а не mb_substr,
			 * пототому что все равно допустимы только латинские буквы.
			 * Не более 11 латинских символов, например: SMS4TEST
			 * и не более 15 цифровых символов, например, 492589655555
			 */
			substr(
				df_output()->transliterate(parent::getSenderName())
				,0, 11
			)
		;
	}

	/** @return string */
	private function getRequestBody() {
		Mage::log($this->getRequestBodyAsSimpleXmlElement()->asXml());
		return df_t()->removeLineBreaks($this->getRequestBodyAsSimpleXmlElement()->asXml());
	}

	/** @return mixed[] */
	private function getRequestBodyAsArray() {
		/** @var mixed[] $result */
		$result = array(
			'security' => array('token' => array(Df_Core_Sxe::ATTR => array('value' => $this->getToken())))
			,'message' => array(
				Df_Core_Sxe::ATTR => array('type' => 'sms')
				,Df_Core_Sxe::CONTENT => array(
					'sender' => $this->getSenderName()
					,'abonent' => array(Df_Core_Sxe::ATTR => array(
						'phone' => $this->getMessage()->getReceiver()
					))
					,'text' => strtr($this->getMessage()->getBody(), array(
						"\r\n" => '/n'
						,"\r" => '/n'
						,"\n" => '/n'
					))
				)
			)
		);
		return $result;
	}
	
	/** @return Df_Core_Sxe */
	private function getRequestBodyAsSimpleXmlElement() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_xml(
					"<?xml version='1.0' encoding='utf-8'?>"
					."<request></request>"
				)
			;
			$this->{__METHOD__}->importArray($this->getRequestBodyAsArray());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getToken() {return df_cfg()->sms()->sms16ru()->getToken($this->store());}

	/** @used-by Df_Sms_Model_Settings_General::getGateClass() */
	const _C = __CLASS__;
	const RM__ID = 'sms16.ru';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Sms_Model_Gate_Sms16Ru
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}