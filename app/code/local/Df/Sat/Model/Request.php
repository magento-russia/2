<?php
abstract class Df_Sat_Model_Request extends Df_Shipping_Model_Request {
	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getHeaders() {
		return array_merge(parent::getHeaders(), array(
			'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
			,'Accept-Encoding' => 'gzip, deflate'
			,'Accept-Language' => 'en-us,en;q=0.5'
			,'Connection' => 'keep-alive'
			,'Host' => $this->getQueryHost()
			,'Referer' => 'http://www.sat.ua/new/ru/services/calculate/'
			,'User-Agent' => Df_Core_Const::FAKE_USER_AGENT
		));
	}
	/**
	 * @override
	 * @return string
	 */
	protected function getQueryHost() {return 'www.sat.ua';}
	/**
	 * @override
	 * @return string
	 */
	protected function getQueryPath() {return '/new/ru/services/calculate/';}
	const _CLASS = __CLASS__;
}