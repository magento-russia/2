<?php
class Df_PonyExpress_Model_Carrier extends Df_Shipping_Carrier {
	/**
	 * @override
	 * @return bool
	 */
	public function hasTheOnlyMethod() {return true;}
	/**
	 * @override
	 * @return bool
	 */
	public function isTrackingAvailable() {return true;}

	/**
	 * @override
	 * @used-by Mage_Shipping_Model_Shipping::collectCarrierRates()
	 * @param Mage_Shipping_Model_Rate_Request $request
  	 * @return Df_Shipping_Carrier|Mage_Shipping_Model_Rate_Result_Error|boolean
	 */
	public function proccessAdditionalValidation(Mage_Shipping_Model_Rate_Request $request) {
		/** @var Df_Shipping_Carrier|Mage_Shipping_Model_Rate_Result_Error|boolean $result */
		$result = parent::proccessAdditionalValidation($request);
		if (
				(false !== $result)
			&&
				!($result instanceof Mage_Shipping_Model_Rate_Result_Error)
		) {
			try {
				/** @var Df_Shipping_Rate_Request $rmRequest */
				$rmRequest = $this->createRateRequest($request);
				df_assert($rmRequest instanceof Df_Shipping_Rate_Request);
				df_assert($rmRequest->getDestinationCity(), 'Укажите город');
			}
			catch (Exception $e) {
				$result = $this->createRateResultError($e);
			}
		}
		return $result;
	}
}