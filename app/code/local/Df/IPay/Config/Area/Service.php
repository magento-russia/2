<?php
/** @method Df_IPay_Method main() */
class Df_IPay_Config_Area_Service extends Df_Payment_Config_Area_Service {
	/**
	 * @override
	 * @return string
	 */
	public function getUrlPaymentPage() {
		/** @var string $result */
		$result =
			$this->isTestMode()
			? parent::getUrlPaymentPage()
			: dfa($this->getMobileNetworkOperatorParams(), 'payment-page')
		;
		df_result_string($result);
		return $result;
	}

	/** @return string|null */
	private function getMobileNetworkOperator() {
		/** @var string|null $result */
		$result = $this->main()->getMobileNetworkOperator();
		if (!is_null($result)) {
			df_result_string($result);
		}
		return $result;
	}

	/** @return array(string => string) */
	private function getMobileNetworkOperatorParams() {
		/** @var array(string => string $result */
		$result =
			dfa(
				$this->constManager()->availablePaymentMethodsAsCanonicalConfigArray()
				,$this->getMobileNetworkOperator()
			)
		;
		df_result_array($result);
		return $result;
	}
}