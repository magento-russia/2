<?php
class Df_PayOnline_Model_Config_Area_Service extends Df_Payment_Config_Area_Service {
	/**
	 * @override
	 * @return string
	 */
	public function getUrlPaymentPage() {
		/** @var string $result */
		$result =
			df_a(
				df_a(
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