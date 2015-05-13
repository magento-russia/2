<?php
class Df_PayOnline_Model_Config_Area_Service extends Df_Payment_Model_Config_Area_Service {
	/**
	 * @override
	 * @return string
	 */
	public function getUrlPaymentPage() {
		/** @var string $result */
		$result =
			df_a(
				df_a(
					$this->getConstManager()->getAvailablePaymentMethodsAsCanonicalConfigArray()
					,$this->getSelectedPaymentMethod()
				)
				,self::KEY__CONST__PAYMENT_METHOD__URL
			)
		;
		df_result_string($result);
		return $result;
	}
	const KEY__CONST__PAYMENT_METHOD__URL = 'url';
}