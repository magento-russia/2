<?php
abstract class Df_YandexMoney_Model_Request_Secondary extends Df_Payment_Model_Request_Secondary {
	/** @return array(string => string) */
	abstract protected function getParamsUnique();

	/** @return string */
	abstract protected function getRequestType();

	/**
	 * @override
	 * @return array(string => string)
	 */
	public function getParams() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getParamsUnique();
			if ($this->getServiceConfig()->isTestMode()) {
				$this->{__METHOD__} = array_merge($this->{__METHOD__}, array(
					'test_payment' => 'true'
					,'test_result' => 'success'
				));
			}
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return Zend_Uri_Http
	 */
	public function getUri() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Zend_Uri::factory('https://money.yandex.ru/api/' . $this->getRequestType())
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return array(string => string|bool|int|float|array(string => string|bool|int|float))
	 */
	protected function getResponseAsArray() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_json_decode($this->getHttpClient()->request()->getBody());
		}
		return $this->{__METHOD__};
	}

	/** @return Zend_Http_Client */
	private function getHttpClient() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = new Zend_Http_Client();
			$this->{__METHOD__}
				->setHeaders(array(
					'Accept' => 'application/json'
					,'Authorization' => 'Bearer ' . $this->getToken()
					,'Content-Type' => 'application/x-www-form-urlencoded'
					,'Host' => $this->getUri()->getHost()
				))
				->setUri($this->getUri()->getUri())
				->setConfig(array('timeout' => 10))
				->setMethod(Zend_Http_Client::POST)
				->setParameterPost($this->getParams())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getToken() {return $this->cfg(self::P__TOKEN);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__TOKEN, self::V_STRING_NE);
	}
	const P__TOKEN = 'token';
}