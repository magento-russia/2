<?php
class Df_RussianPost_Model_Official_Request_International extends Df_Shipping_Model_Request {
	/** @return float */
	public function getRate() {
		if (!isset($this->{__METHOD__})) {
			/** @var phpQueryObject $pqRate */
			$pqRate = $this->response()->pq('#TarifValue');
			$this->{__METHOD__} = rm_float(df_trim($pqRate->text()));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @param Zend_Http_Client $httpClient
	 * @return Df_RussianPost_Model_Official_Request_International
	 */
	protected function adjustHttpClient(Zend_Http_Client $httpClient) {$httpClient->setCookieJar(true);}

	/**
	 * @override
	 * @return array(string = string)
	 */
	protected function getQueryParams() {
		return array(
			'viewPost' => 36 // ценная посылка
			,'viewPostName' => 'Ценная посылка'
			,'typePost' => 2 // авиа
			,'typePostName' => 'АВИА'
			,'postOfficeId' => 0
			,'countryCode' => $this->getDestinationCountry()->getNumericCode()
			,'countryCodeName' => mb_strtoupper($this->getDestinationCountry()->getName())
			,'weight' => strval($this->getWeightInGrammes())
			,'value1' => strval($this->getDeclaredValue())
		);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryHost() {return 'www.russianpost.ru';}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryPath() {return '/autotarif/Autotarif.aspx';}

	/**
	 * @override
	 * @return string
	 */
	protected function getRequestMethod() {return Zend_Http_Client::GET;}

	/**
	 * @override
	 * @return string
	 */
	protected function getResponseAsTextInternal() {
		/** @var string $result */
		$result = parent::getResponseAsTextInternal();
		/** @var int $counter */
		$counter = 0;
		/** @var string $originalUri */
		$originalUri = $this->getUri()->getUri();
		while (5 > $counter) {
			$counter++;
			if (rm_contains($result, 'onload="document.myform.submit();"')) {
				/** @var int $verificationCode */
				$verificationCode = $this->parseVerificationCode($result);
				$this->getHttpClient()
					->setHeaders(
						array(
							'Referer' => 'http://www.russianpost.ru/autotarif/Autotarif.aspx'
						)
					)
					->setUri('http://www.russianpost.ru/autotarif/Autotarif.aspx')
					->setMethod(Zend_Http_Client::POST)
					->setParameterPost(array('key' => $verificationCode))
				;
				/** @var string $result */
				$result = $this->getHttpClient()->request()->getBody();
			}
			else if(rm_contains($result, 'window.location.replace(window.location.toString())')) {
				$this->getHttpClient()
					->setHeaders(
						array('Referer' => 'http://www.russianpost.ru/autotarif/Autotarif.aspx')
					)
					->setUri($originalUri)
					->setMethod(Zend_Http_Client::GET)
				;
				/** @var string $result */
				$result = $this->getHttpClient()->request()->getBody();
			}
			else {
				break;
			}
		}
		$result = str_replace('charset=windows-1251', 'charset=UTF-8', $result);
		return $result;
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getHeaders() {
		return array(
			'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8'
			,'Accept-Encoding' => 'gzip,deflate'
			,'Cache-Control' => 'no-cache'
			,'Connection' => 'keep-alive'
			,'Host' => 'www.russianpost.ru'
			,'Pragma' => 'no-cache'
			,'Referer' => 'http://www.russianpost.ru/autotarif/Autotarif.aspx'
			,'User-Agent' => Df_Core_Const::FAKE_USER_AGENT
		);
	}

	/**
	 * @override
	 * @return array
	 */
	protected function getRequestConfuguration() {
		return array(
			/**
			 * Не знаю, насколько это обязательно, но не повредит точно
			 * @link http://framework.zend.com/manual/1.12/en/zend.http.client.advanced.html
			 */
			'keepalive' => true
		);
	}

	/** @return int */
	private function getDeclaredValue() {return $this->cfg(self::P__DECLARED_VALUE);}

	/** @return Df_Directory_Model_Country */
	private function getDestinationCountry() {return $this->cfg(self::P__DESTINATION_COUNTRY);}

	/** @return int */
	private function getWeightInGrammes() {return $this->cfg(self::P__WEIGHT_IN_GRAMMES);}

	/**
	 * @param string $responseAsText
	 * @return int
	 */
	private function parseVerificationCode($responseAsText) {
		return rm_preg_match_int('#value\=\"(\d+)\"#', $responseAsText);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__WEIGHT_IN_GRAMMES, self::V_INT)
			->_prop(self::P__DECLARED_VALUE, self::V_INT)
			->_prop(self::P__DESTINATION_COUNTRY, Df_Directory_Model_Country::_CLASS)
		;
	}
	const _CLASS = __CLASS__;
	const P__DECLARED_VALUE = 'declared_value';
	const P__DESTINATION_COUNTRY = 'destination_country';
	const P__WEIGHT_IN_GRAMMES = 'weight_in_grammes';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_RussianPost_Model_Official_Request_International
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}


