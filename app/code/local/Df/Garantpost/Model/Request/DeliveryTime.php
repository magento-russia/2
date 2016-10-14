<?php
class Df_Garantpost_Model_Request_DeliveryTime extends Df_Garantpost_Model_Request {
	/**
	 * @override
	 * @return string
	 */
	protected function getRequestMethod() {
		return Zend_Http_Client::POST;
	}
	const _C = __CLASS__;
}