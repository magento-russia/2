<?php
class Df_Megapolis_Model_Map extends Df_Core_Model {
	/**
	 * @param string $cityName
	 * @return array(string => Df_Megapolis_Model_Location[])
	 */
	public function getByCity($cityName) {
		$cityName = Df_Megapolis_Model_Location::i()->normalizeName($cityName);
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
	 * @return Df_Megapolis_Model_Location[]
	 * @throws Exception
	 */
	private function requestLocationsFromServer($cityName) {
		try {
			/** @var Df_Megapolis_Model_Location[] $result */
			$result = array();
			/** @var Zend_Uri_Http $uri */
			$uri = Zend_Uri::factory('http');
			$uri->setHost('www.megapolis-exp.ru');
			$uri->setPath('/api/');
			$uri->setQuery(array('get_city' => $cityName));
			/** @var Zend_Http_Client $httpClient */
			$httpClient = new Zend_Http_Client();
			$httpClient->setUri($uri);
			/** @var Zend_Http_Response $response */
			$response = $httpClient->request(Zend_Http_Client::GET);
			/** @var string $responseAsText */
			$responseAsText = $response->getBody();
			/**
			 * Если ни одного населённого пункта не найдено,
			 * то МЕГАПОЛИС возвращает пустой ответ
			 */
			if ($responseAsText) {
				/** @var string $responseAsJson */
				$responseAsJson = rm_sprintf('[%s]', implode("\n" . ',',
					df_explode_n(strtr($responseAsText, array('=>' => ':'))))
				);
				/** @var string[] $responseAsArray */
				$responseAsArray = Zend_Json::decode($responseAsJson);
				df_assert_array($responseAsArray);
				foreach ($responseAsArray as $locationAsArray) {
					/** @var array(string => string|int|null) $locationAsArray */
					df_assert_array($locationAsArray);
					$result[]= Df_Megapolis_Model_Location::i($locationAsArray);
				}
			}
		}
		catch (Exception $e) {
			Mage::logException($e);
			throw $e;
		}
		return $result;
	}

	const _C = __CLASS__;
	/** @return Df_Megapolis_Model_Map */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}