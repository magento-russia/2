<?php
class Df_Licensor_Model_Server_Time_MagentoProRu extends Df_Licensor_Model_Server_Time {
	/**
	 * @override
	 * @return string
	 */
	protected function retrieveTimeAsString() {
		$httpClient = new Zend_Http_Client();
		/** @var Zend_Uri_Http $uri */
		$uri = Zend_Uri::factory('http');
		$uri->setHost('server.magento-pro.ru');
		$uri->setPath('/time.php');
		$httpClient
			->setHeaders(array(
				'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
				,'Accept-Encoding' => 'gzip, deflate'
				,'Accept-Language' => 'en-us,en;q=0.5'
				,'Cache-Control' => 'no-cache'
				,'Connection' => 'close'
			))
			->setUri($uri)
			->setConfig(array('timeout' => 3))
		;
		/** @var Zend_Http_Response $response */
		$response = $httpClient->request(Zend_Http_Client::GET);
		/** @var string $result */
		$result = $response->getBody();
		df_result_string($result);
		return $result;
	}

	/** @return Df_Licensor_Model_Server_Time_MagentoProRu */
	public static function i() {return new self;}
}