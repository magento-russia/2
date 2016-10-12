<?php
abstract class Df_Yandex_Model_OAuth extends Df_Core_Model {
	/**
	 * @abstract
	 * @param array(string => string) $response
	 * @return Df_Yandex_Model_OAuth
	 * @throws Exception
	 */
	abstract protected function checkResponse(array $response);
	/** @return string */
	abstract protected function getUriAsString();

	/** @return string */
	public function getToken() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_a($this->getResponseAsArray(), 'access_token');
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => string) */
	protected function getAdditionalParams() {return array();}

	/** @return string */
	private function getAppId() {return $this->cfg(self::P__APP_ID);}

	/** @return string */
	private function getAppPassword() {return $this->cfg(self::P__APP_PASSWORD);}

	/** @return Zend_Http_Client */
	private function getHttpClient() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = new Zend_Http_Client();
			$this->{__METHOD__}
				->setHeaders(array(
					'Accept' => 'application/json'
					,'Content-Type' => 'application/x-www-form-urlencoded'
					,'Host' => $this->getUri()->getHost()
				))
				->setUri($this->getUri()->getUri())
				->setConfig(array('timeout' => 10))
				->setMethod(Zend_Http_Client::POST)
				->setParameterPost(array_merge(
					array(
						'grant_type' => 'authorization_code'
						,'code' => $this->getTokenTempopary()
						,'client_id' => $this->getAppId()
						,'client_secret' => $this->getAppPassword()
					)
					,$this->getAdditionalParams()
				))
			;
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => string) */
	private function getResponseAsArray() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_json_decode($this->getHttpClient()->request()->getBody());
			df_result_array($this->{__METHOD__});
			$this->checkResponse($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getTokenTempopary() {return $this->cfg(self::P__TOKEN_TEMPOPARY);}

	/** @return Zend_Uri_Http */
	private function getUri() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Zend_Uri::factory($this->getUriAsString());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__APP_ID, self::V_STRING_NE)
			->_prop(self::P__APP_PASSWORD, self::V_STRING_NE)
			->_prop(self::P__TOKEN_TEMPOPARY, self::V_STRING_NE)
		;
	}
	const P__APP_ID = 'app_id';
	const P__APP_PASSWORD = 'app_password';
	const P__TOKEN_TEMPOPARY = 'token_tempopary';
}