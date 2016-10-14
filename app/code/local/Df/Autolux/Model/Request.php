<?php
abstract class Df_Autolux_Model_Request extends Df_Shipping_Model_Request {
	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getHeaders() {
		return array(
			'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
			,'Accept-Encoding' => 'gzip, deflate'
			,'Accept-Language' => 'en-us,en;q=0.5'
			,'Connection' => 'keep-alive'
			,'Host' => $this->getQueryHost()
			,'Referer' => 'http://autolux.ua/Kalkulyator_stoimosti'
		) + parent::getHeaders();
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryHost() {return 'autolux.ua';}
}