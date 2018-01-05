<?php
class Df_Spsr_Model_Map extends Df_Core_Model_DestructableSingleton {
	/**
	 * @param string $cityName
	 * @return array(string => Df_Spsr_Model_Location[])
	 */
	public function getByCity($cityName) {
		$cityName = Df_Spsr_Model_Location::i()->normalizeName($cityName);
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
	 * 2018-01-05
	 * 1) "Модуль «СПСР-Экспресс» стал приводить к сбою:
	 * «Значение переменной забраковано проверяющим «Df_Zf_Validate_Array»»,
	 * потому что СПСР-Экспресс изменила API": https://github.com/magento-russia/2/issues/11
	 * 2) "[СПСР-Экспресс] Пример ответа на `https://www.spsr.ru/webapi/autocomplete_city?city=<value>`":
	 * https://df.tips/t/294
	 * @param string $cityName
	 * @return Df_Spsr_Model_Location[]
	 * @throws Exception
	 */
	private function requestLocationsFromServer($cityName) {
		try {
			$result = array(); /** @var Df_Spsr_Model_Location[] $result */
			$uri = Zend_Uri::factory('https'); /** @var Zend_Uri_Http $uri */
			$uri->setHost('www.spsr.ru');
			$uri->setPath('/webapi/autocomplete_city');
			$uri->setQuery(array('city' => $cityName));
			$httpClient = new Zend_Http_Client; /** @var Zend_Http_Client $httpClient */
			$httpClient->setUri($uri);
			$response = $httpClient->request(Zend_Http_Client::GET); /** @var Zend_Http_Response $response */
			$responseAsJson = $response->getBody(); /** @var string $responseAsJson */
			df_assert_string_not_empty($responseAsJson);
			$responseAsJson = df_text()->bomRemove($responseAsJson);
			$responseAsArray = df_json_decode($responseAsJson); /** @var string[] $responseAsArray */
			df_assert_array($responseAsArray);
			foreach ($responseAsArray as $locationAsArray) {
				/** @var array(string => string|int|null) $locationAsArray */
				df_assert_array($locationAsArray);
				$result[]= Df_Spsr_Model_Location::i($locationAsArray);
			}
		}
		catch(Exception $e) {
			Mage::logException($e);
			throw $e;
		}
		return $result;
	}

	const _CLASS = __CLASS__;

	/** @return Df_Spsr_Model_Map */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}