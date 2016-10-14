<?php
abstract class Df_EuroExpress_Model_Request extends Df_Shipping_Model_Request {
	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getHeaders() {
		return array(
			'Accept' => '*/*'
			,'Accept-Encoding' => 'gzip, deflate'
			,'Accept-Language' => 'en-us,en;q=0.5'
			,'Cache-Control' => 'no-cache'
			,'Connection' => 'keep-alive'
			,'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
			,'Host' => $this->getQueryHost()
			,'Pragma' => 'no-cache'
			,'Referer' => 'http://www.euroexpress.net.ua/ru/services_calc'
		) + parent::getHeaders();
	}
	/**
	 * @override
	 * @return string
	 */
	protected function getQueryHost() {return 'www.euroexpress.net.ua';}
	/**
	 * @override
	 * @return string
	 */
	protected function getQueryPath() {return '/ru/services_calc';}
}