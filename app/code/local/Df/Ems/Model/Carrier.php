<?php
class Df_Ems_Model_Carrier extends Df_Shipping_Model_Carrier {
	/**
	 * @override
	 * @return bool
	 */
	public function hasTheOnlyMethod() {return true;}
	/**
	 * Обратите внимание, что при браковке запроса в методе proccessAdditionalValidation
	 * модуль может показать на экране оформления заказа диагностическое сообщение,
	 * вернув из этого метода объект класса Mage_Shipping_Model_Rate_Result_Error.
	 * При браковке запроса в методе collectRates модуль такой возможности лишён.
	 * @override
	 * @param Mage_Shipping_Model_Rate_Request $request
  	 * @return Df_Shipping_Model_Carrier|Mage_Shipping_Model_Rate_Result_Error|boolean
	 */
	public function proccessAdditionalValidation(Mage_Shipping_Model_Rate_Request $request) {
		/** @var Df_Shipping_Model_Carrier|Mage_Shipping_Model_Rate_Result_Error|boolean $result */
		$result = parent::proccessAdditionalValidation($request);
		if (
				(false !== $result)
			&&
				!($result instanceof Mage_Shipping_Model_Rate_Result_Error)
		) {
			try {
				/** @var Df_Shipping_Model_Rate_Request $rmRequest */
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
			catch(Exception $e) {
				$result = Df_Shipping_Model_Rate_Result_Error::i($this, rm_ets($e));
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