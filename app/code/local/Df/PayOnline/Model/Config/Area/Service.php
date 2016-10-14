<?php
class Df_PayOnline_Model_Config_Area_Service extends Df_Payment_Config_Area_Service {
	/**
	 * @override
	 * @return string
	 */
	public function getUrlPaymentPage() {
		/** @var string $result */
		$result =
			dfa(
				dfa(
					$this->constManager()->getAvailablePaymentMethodsAsCanonicalConfigArray()
					,$this->getSelectedPaymentMethod()
				)
				,'url'
			)
		;
		df_result_string($result);
		return $result;
	}
}