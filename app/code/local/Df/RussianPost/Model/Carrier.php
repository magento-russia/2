<?php
class Df_RussianPost_Model_Carrier extends Df_Shipping_Carrier {
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
				df_assert(
					$rmRequest->getOriginPostalCode()
					,'Администратор магазина должен указать почтовый индекс магазина в графе
					«Система» → «Настройки» → «Продажи» → «Доставка:
					общие настройки»→ «Расположение магазина» → «Почтовый индекс».'
				);
				df_assert($rmRequest->getDestinationPostalCode(), 'Укажите почтовый индекс.');
			}
			catch (Exception $e) {
				$result = $this->createRateResultError($e);
			}
		}
		return $result;
	}
}