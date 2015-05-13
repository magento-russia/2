<?php
abstract class Df_Cdek_Model_Request extends Df_Shipping_Model_Request {
	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getHeaders() {
		return array_merge(parent::getHeaders(), array(
			'Host' => $this->getQueryHost()
			,'Referer' => df_current_url()
			,'User-Agent' => 'Российская сборка Magento ' . rm_version_full()
		));
	}
	/**
	 * @override
	 * @return string
	 */
	protected function getQueryHost() {return 'api.edostavka.ru';}
	const _CLASS = __CLASS__;
}