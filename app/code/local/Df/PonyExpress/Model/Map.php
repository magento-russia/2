<?php
class Df_PonyExpress_Model_Map extends Df_Core_Model_DestructableSingleton {
	/**
	 * @param string $cityName
	 * @return array(string => Df_PonyExpress_Model_Location[])
	 */
	public function getByCity($cityName) {
		$cityName = Df_PonyExpress_Model_Location::i()->normalizeName($cityName);
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
	 * @return Df_PonyExpress_Model_Location[]
	 * @throws Exception
	 */
	private function requestLocationsFromServer($cityName) {
		try {
			/** @var Df_PonyExpress_Model_Location[] $result */
			$result = array();
			/** @var Zend_Uri_Http $uri */
			$uri = Zend_Uri::factory('http');
			$uri->setHost('www.ponyexpress.ru');
			$uri->setPath('/autocomplete/city');
			$uri->setQuery(array('term' => $cityName));
			/** @var Zend_Http_Client $httpClient */
			$httpClient = new Zend_Http_Client();
			$httpClient
				->setHeaders(
					array(
						'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
						,'Accept-Encoding' => 'gzip, deflate'
						,'Accept-Language' => 'en-US,en;q=0.5'
						,'Connection' => 'keep-alive'
						,'Host' => 'www.ponyexpress.ru'
						,'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:24.0) Gecko/20100101 Firefox/24.0'
					)
				)
				->setUri($uri)
				->setConfig(array('timeout' => 5))
			;
			/** @var Zend_Http_Response $response */
			$response = $httpClient->request(Zend_Http_Client::GET);
			/** @var string $responseAsJson */
			$responseAsJson = $response->getBody();
			df_assert_string_not_empty($responseAsJson);
			$responseAsJson = str_replace('﻿', '' , $responseAsJson);
			/** @var string[] $responseAsArray */
			$responseAsArray = Zend_Json::decode($responseAsJson);
			df_assert_array($responseAsArray);
			/**
			 * Для города Birmingham сервер возвращает странный массив
			 * с единственным пустым элементом:
				Array
				(
					[0] =>
				)
			 */
			$responseAsArray = df_clean($responseAsArray);
			foreach ($responseAsArray as $locationAsText) {
				/** @var string $locationAsText */
				df_assert_string_not_empty($locationAsText);
				$result[]= Df_PonyExpress_Model_Location::i($locationAsText);
			}
		}
		catch(Exception $e) {
			Mage::logException($e);
			throw $e;
		}
		return $result;
	}

	const _CLASS = __CLASS__;

	/** @return Df_PonyExpress_Model_Map */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}