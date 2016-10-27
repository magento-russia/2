<?php
namespace Df\Alfabank\Config\Area;
class Service extends \Df\Payment\Config\Area\Service {
	/**
	 * @used-by \Df\Alfabank\Method::getRegistrationResponseJson()
	 * @param array(string => string|int) $params
	 * @return \Zend_Uri_Http
	 */
	public function getRegistrationUri(array $params) {
		/** @var \Zend_Uri_Http $result */
		$result = $this->getUri($this->isCardPaymentActionAuthorize() ? 'registerPreAuth' : 'register');
		$result->setQuery($params);
		return $result;
	}

	/**
	 * @used-by getUriPayment()
	 * @used-by \Df\Alfabank\Request\Secondary::getUri()
	 * @param string $action
	 * @return \Zend_Uri_Http
	 */
	public function getUri($action) {
		/** @var \Zend_Uri_Http $result */
		$result = \Zend_Uri::factory('https');
		$result->setHost(($this->isTestMode() ? 'test' : 'engine') . '.paymentgate.ru');
		$result->setPath(strtr('/{path}/rest/{action}.do', array(
			'{path}' => $this->isTestMode() ? 'testpayment' : 'payment'
			,'{action}' => $action
		)));
		return $result;
	}
}