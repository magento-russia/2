<?php
namespace Df\YandexMoney\Request;
abstract class Secondary extends \Df\Payment\Request\Secondary {
	/** @return array(string => string) */
	abstract protected function getParamsUnique();

	/** @return string */
	abstract protected function getRequestType();

	/**
	 * @override
	 * @return \Zend_Uri_Http
	 */
	public function getUri() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				\Zend_Uri::factory('https://money.yandex.ru/api/' . $this->getRequestType())
			;
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
		/** @var array(string => string|int) $result */
		$result = $this->getParamsUnique();
		if ($this->configS()->isTestMode()) {
			$result = array('test_payment' => 'true', 'test_result' => 'success') + $result;
		}
		return $result;
	}

	/**
	 * @override
	 * @return array(string => string|bool|int|float|array(string => string|bool|int|float))
	 */
	protected function getResponseAsArray() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = \Zend_Json::decode($this->getHttpClient()->request()->getBody());
		}
		return $this->{__METHOD__};
	}

	/** @return \Zend_Http_Client */
	private function getHttpClient() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = new \Zend_Http_Client();
			$this->{__METHOD__}
				->setHeaders(array(
					'Accept' => 'application/json'
					,'Authorization' => 'Bearer ' . $this->getToken()
					,'Content-Type' => 'application/x-www-form-urlencoded'
					,'Host' => $this->getUri()->getHost()
				))
				->setUri($this->getUri()->getUri())
				->setConfig(array('timeout' => 10))
				->setMethod(\Zend_Http_Client::POST)
				->setParameterPost($this->params())
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
		$this->_prop(self::P__TOKEN, DF_V_STRING_NE);
	}
	const P__TOKEN = 'token';
}