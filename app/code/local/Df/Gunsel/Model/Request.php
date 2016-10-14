<?php
abstract class Df_Gunsel_Model_Request extends Df_Shipping_Model_Request {
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
			,'Referer' => 'http://www.gunsel.com.ua/index.php?option=com_content&view=article&id=9&Itemid=16'
		)  + parent::getHeaders();
	}
	/**
	 * @override
	 * @return string
	 */
	protected function getQueryHost() {return 'gunsel.com.ua';}
	/**
	 * @override
	 * @return array(string => int|string)
	 */
	protected function getQueryParams() {
		return array(
			'option' => 'com_content'
			,'view' => 'article'
			,'id' => 9
			,'Itemid' => 16
			,'lang' => 'ru'
		);
	}
	/**
	 * @override
	 * @return string
	 */
	protected function getQueryPath() {return '/index.php';}
}