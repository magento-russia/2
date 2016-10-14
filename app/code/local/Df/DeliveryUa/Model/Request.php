<?php
abstract class Df_DeliveryUa_Model_Request extends Df_Shipping_Model_Request {
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
			,'Referer' => 'http://www.delivery-auto.com/ru/index.php?id=7068&show=29952'
		) + parent::getHeaders();
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryHost() {return 'www.delivery-auto.com';}

	/**
	 * Почему то phpQuery конкретно для документов сайта www.delivery-auto.com
	 * некорректно работает с кодировкой Windows-1251
	 * @override
	 * @return string
	 */
	protected function getResponseAsTextInternal() {
		return str_replace(
			'charset=windows-1251', 'charset=utf-8', rm_1251_from(parent::getResponseAsTextInternal())
		);
	}
}