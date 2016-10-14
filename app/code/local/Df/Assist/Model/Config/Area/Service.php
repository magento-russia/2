<?php
class Df_Assist_Model_Config_Area_Service extends Df_Payment_Config_Area_Service {
	/**
	 * @override
	 * @return string
	 */
	public function getUrlPaymentPage() {return $this->getUrl('payment_page');}

	/**
	 * @used-by getUrl()
	 * @return string
	 */
	private function getDomain() {
		/** @var string $result */
		$result = $this->isTestMode() ? $this->getConst('domain') : $this->getVar('domain');
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
		$uri->setPath('/' . $this->constManager()->getUrl($type, false));
		/** @var string $result */
		$result = $uri->getUri();
		df_result_string_not_empty($result);
		return $result;
	}
}