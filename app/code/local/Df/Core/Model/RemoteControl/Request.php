<?php
abstract class Df_Core_Model_RemoteControl_Request extends Df_Core_Model_Abstract {
	/**
	 * @abstract
	 * @return Zend_Uri_Http
	 */
	abstract protected function createUri();

	/** @return Df_Core_Model_RemoteControl_Message_Response */
	public function getMessageResponse() {return $this->_messageResponse;}
	/** @var Df_Core_Model_RemoteControl_Message_Response */
	private $_messageResponse;

	/** @return Df_Core_Model_RemoteControl_Message_Response */
	public function send() {
		/** @var Df_Core_Model_RemoteControl_Message_Response $result */
		$result = null;
		/** @var Zend_Http_Response $response */
		$response = $this->getHttpClient()->request(Zend_Http_Client::POST);
		/** @var Df_Core_Model_RemoteControl_Message_Response $result */
		$result = Df_Core_Model_RemoteControl_MessageSerializer_Http::restoreMessageResponse($response);
		df_assert($result instanceof Df_Core_Model_RemoteControl_Message_Response);
		if (!$result->isOk()) {
			df_error($result->getText());
		}
		// Надо бы ещё проверять, что данные пришли именно от нашего сервера
		// (то есть, контролировать подпись данных)
		$this->_messageResponse = $result;
		return $result;
	}

	/** @return Zend_Http_Client */
	protected function createHttpClient() {
		/** @var Zend_Http_Client $result */
		$result = new Zend_Http_Client();
		Df_Core_Model_RemoteControl_MessageSerializer_Http
			::serializeMessageRequest($result, $this->getMessageRequest())
		;
		$result
			->setHeaders(array(
				'Accept' => 'application/octet-stream'
				,'Accept-Encoding' => 'gzip, deflate'
				,'Accept-Language' => 'en-us,en;q=0.5'
				,'Connection' => 'keep-alive'
				// Маскируемся доменом того магазина, к которому обращаемся
				,'Host' => $this->getUri()->getHost()
				// Маскируемся адресом того магазина, к которому обращаемся
				,'Referer' => Mage::app()->getRequest()->getRequestUri()
				,'User-Agent' => Df_Core_Const::FAKE_USER_AGENT
			))
			->setUri($this->getUri())
			->setConfig(array('timeout' => 10))
		;
		return $result;
	}

	/** @return Df_Core_Model_RemoteControl_Message_Request */
	protected function getMessageRequest() {return $this->cfg(self::P__MESSAGE_REQUEST);}

	/** @return Zend_Uri_Http */
	protected function getUri() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->createUri();
		}
		return $this->{__METHOD__};
	}

	/** @return Zend_Http_Client */
	private function getHttpClient() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->createHttpClient();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__MESSAGE_REQUEST, Df_Core_Model_RemoteControl_Message_Request::_CLASS);
	}
	const _CLASS = __CLASS__;
	const P__MESSAGE_REQUEST = 'message_request';
}