<?php
class Df_Dellin_Model_Map extends Df_Core_Model_DestructableSingleton {
	/**
	 * @param string $cityName
	 * @return array(string => Df_Dellin_Model_Location[])
	 */
	public function getByCity($cityName) {
		$cityName = Df_Dellin_Model_Location::i()->normalizeName($cityName);
		if (!isset($this->{__METHOD__}[$cityName])) {
			$this->{__METHOD__}[$cityName] = $this->requestLocationsFromServer($cityName);
			$this->markCachedPropertyAsModified(__METHOD__);
		}
		return $this->{__METHOD__}[$cityName];
	}

	/**             
	 * @override
	 * @return string[]
	 */
	protected function getPropertiesToCache() {return self::m(__CLASS__, 'getByCity');}

	/**
	 * @param string $cityName
	 * @return Df_Dellin_Model_Location[]
	 * @throws Exception
	 */
	private function requestLocationsFromServer($cityName) {
		try {
			/** @var Df_Dellin_Model_Location[] $result */
			$result = array();
			/** @var Zend_Uri_Http $uri */
			$uri = Zend_Uri::factory('http');
			$uri->setHost('public.services.dellin.ru');
			$uri->setPath('/calculatorTool2/js/index.html');
			$uri
				->setQuery(
					array(
						'answerType' => 'json'
						,'mode' => 'getPlaces'
						,'q' => $cityName
					)
				)
			;
			/** @var Zend_Http_Client $httpClient */
			$httpClient = new Zend_Http_Client();
			$httpClient->setUri($uri);
			/** @var Zend_Http_Response $response */
			$response = $httpClient->request(Zend_Http_Client::GET);
			/** @var string $responseAsJson */
			$responseAsJson = $response->getBody();
			df_assert_string_not_empty($responseAsJson);
			$responseAsJson = df_text()->bomRemove($responseAsJson);
			/** @var string[] $responseAsArray */
			$responseAsArray = Zend_Json::decode($responseAsJson);
			df_assert_array($responseAsArray);
			foreach ($responseAsArray as $locationAsArray) {
				/** @var array(string => string|int|null) $locationAsArray */
				df_assert_array($locationAsArray);
				$result[]= Df_Dellin_Model_Location::i($locationAsArray);
			}
		}
		catch(Exception $e) {
			Mage::logException($e);
			throw $e;
		}
		return $result;
	}

	const _CLASS = __CLASS__;
	/** @return Df_Dellin_Model_Map */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}