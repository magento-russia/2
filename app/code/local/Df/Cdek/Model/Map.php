<?php
class Df_Cdek_Model_Map extends Df_Core_Model {
	/**
	 * @param string $cityName
	 * @return array(string => Df_Cdek_Model_Location[])
	 */
	public function getByCity($cityName) {
		$cityName = Df_Cdek_Model_Location::i(array())->normalizeName($cityName);
		if (!isset($this->{__METHOD__}[$cityName])) {
			$this->{__METHOD__}[$cityName] = $this->requestLocationsFromServer($cityName);
			$this->markCachedPropertyAsModified(__METHOD__);
		}
		return $this->{__METHOD__}[$cityName];
	}

	/**
	 * @override
	 * @see Df_Core_Model::cachedGlobalObjects()
	 * @return string[]
	 */
	protected function cachedGlobalObjects() {return self::m(__CLASS__, 'getByCity');}

	/**
	 * @param string $cityName
	 * @return Df_Cdek_Model_Location[]
	 * @throws Exception
	 */
	private function requestLocationsFromServer($cityName) {
		/** @var Df_Cdek_Model_Location[] $result */
		$result = array();
		try {
			/** @var Zend_Uri_Http $uri */
			$uri = Zend_Uri::factory('http');
			$uri->setHost('api.edostavka.ru');
			$uri->setPath('/city/getListByTerm/jsonp.php');
			$uri->setQuery(array('q' => $cityName));
			/** @var Zend_Http_Client $httpClient */
			$httpClient = new Zend_Http_Client();
			$httpClient->setUri($uri);
			/** @var Zend_Http_Response $response */
			$response = $httpClient->request(Zend_Http_Client::GET);
			/** @var string $responseAsJson */
			$responseAsJson = $response->getBody();
			df_assert_string_not_empty($responseAsJson);
			$responseAsJson = df_trim($responseAsJson, '()');
			/** @var string[] $responseAsArray */
			$responseAsArray = Zend_Json::decode($responseAsJson);
			df_assert_array($responseAsArray);
			$responseAsArray = df_a($responseAsArray, 'geonames');
			df_assert_array($responseAsArray);
			/** @uses Df_Cdek_Model_Location::i() */
			$result = array_map(array(Df_Cdek_Model_Location::_C, 'i'), $responseAsArray);
		}
		catch (Exception $e) {
			Mage::logException($e);
			df_error($e);
		}
		return $result;
	}

	const _C = __CLASS__;
	/** @return Df_Cdek_Model_Map */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}