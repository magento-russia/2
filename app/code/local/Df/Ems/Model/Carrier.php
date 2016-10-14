<?php
class Df_Ems_Model_Carrier extends Df_Shipping_Carrier {
	/**
	 * @override
	 * @return bool
	 */
	public function hasTheOnlyMethod() {return true;}
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
				if (self::MAX_WEIGHT < $rmRequest->getWeightInKilogrammes()) {
					df_error(
						'Доставка службой EMS для данного заказа недоступна,'
						.' потому что максимальный вес груза для службы EMS составляет %.1f кг.,'
						.' а вес Вашего заказа — %.1f кг.'
						,self::MAX_WEIGHT
						,$rmRequest->getWeightInKilogrammes()
					);
				}
			}
			catch (Exception $e) {
				$result = $this->createRateResultError($e);
			}
		}
		return $result;
	}
	/**
	 * @override
	 * @return bool
	 */
	public function isTrackingAvailable() {return true;}

	const MAX_WEIGHT = 31.5;
}