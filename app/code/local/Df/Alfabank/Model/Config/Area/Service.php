<?php
class Df_Alfabank_Model_Config_Area_Service extends Df_Payment_Model_Config_Area_Service {
	/**
	 * @param string $action
	 * @return Zend_Uri
	 */
	public function getUri($action) {
		/** @var Zend_Uri_Http $result */
		$result = Zend_Uri::factory('https');
		$result->setHost(df_concat($this->isTestMode() ? 'test' : 'engine', '.paymentgate.ru'));
		$result
			->setPath(
				strtr(
					'/{path}/rest/{action}.do'
					,array(
						'{path}' => $this->isTestMode() ? 'testpayment' : 'payment'
						,'{action}' => $action
					)
				)
			)
		;
		return $result;
	}

	/** @return Zend_Uri */
	public function getUriPayment() {
		return
			$this->getUri(
				$this->isCardPaymentActionAuthorize() ? 'registerPreAuth' : 'register'
			)
		;
	}
}