<?php
class Df_PayOnline_Config_Area_Service extends \Df\Payment\Config\Area\Service {
	/**
	 * @override
	 * @return string
	 */
	public function getUrlPaymentPage() {
		/** @var string $result */
		$result =
			dfa(
				dfa(
					$this->constManager()->availablePaymentMethodsAsCanonicalConfigArray()
					,$this->getSelectedPaymentMethod()
				)
				,'url'
			)
		;
		df_result_string($result);
		return $result;
	}
}