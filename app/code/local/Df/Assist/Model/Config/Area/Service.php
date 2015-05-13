<?php
class Df_Assist_Model_Config_Area_Service extends Df_Payment_Model_Config_Area_Service {
	/**
	 * @override
	 * @return string
	 */
	public function getUrlPaymentPage() {
		return $this->getUrl(self::KEY__CONST__URL__PAYMENT_PAGE);
	}
	/** @return string */
	private function getDomain() {
		/** @var string $result */
		$result =
			$this->isTestMode()
			? $this->getConst(self::KEY__CONST__DOMAIN)
			: $this->getVar(self::KEY__VAR__DOMAIN)
		;
		df_result_string_not_empty($result);
		return $result;
	}

	/**
	 * @param string $type
	 * @return string
	 */
	private function getUrl($type) {
		df_param_string_not_empty($type, 0);
		/** @var Zend_Uri_Http $uri */
		$uri = Zend_Uri::factory();
		$uri->setHost($this->getDomain());
		$uri->setPath('/' . $this->getConstManager()->getUrl($type, false));
		/** @var string $result */
		$result = $uri->getUri();
		df_result_string_not_empty($result);
		return $result;
	}
	const KEY__CONST__DOMAIN = 'domain';
	const KEY__CONST__URL__PAYMENT_PAGE = 'payment_page';
	const KEY__VAR__DOMAIN = 'domain';
}