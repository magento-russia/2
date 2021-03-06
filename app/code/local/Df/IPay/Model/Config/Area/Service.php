<?php
class Df_IPay_Model_Config_Area_Service extends Df_Payment_Model_Config_Area_Service {
	/**
	 * @override
	 * @return string
	 */
	public function getUrlPaymentPage() {
		/** @var string $result */
		$result =
			$this->isTestMode()
			? parent::getUrlPaymentPage()
			: df_a($this->getMobileNetworkOperatorParams(), 'payment-page')
		;
		df_result_string($result);
		return $result;
	}

	/** @return string|null */
	private function getMobileNetworkOperator() {
		/** @var string|null $result */
		$result = $this->getPaymentMethod()->getMobileNetworkOperator();
		if (!is_null($result)) {
			df_result_string($result);
		}
		return $result;
	}

	/** @return array(string => string) */
	private function getMobileNetworkOperatorParams() {
		/** @var array(string => string $result */
		$result =
			df_a(
				$this->getConstManager()->getAvailablePaymentMethodsAsCanonicalConfigArray()
				,$this->getMobileNetworkOperator()
			)
		;
		df_result_array($result);
		return $result;
	}

	/** @return Df_IPay_Model_Payment */
	private function getPaymentMethod() {
		return $this->getVarManager()->getPaymentMethod();
	}
}