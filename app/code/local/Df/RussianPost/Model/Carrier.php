<?php
class Df_RussianPost_Model_Carrier extends Df_Shipping_Model_Carrier {
	/**
	 * @override
	 * @return string
	 */
	public function getRmId() {return 'russian-post';}
	/**
	 * @override
	 * @return bool
	 */
	public function isTrackingAvailable() {return true;}
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
				df_assert(
					$rmRequest->getOriginPostalCode()
					,'Администратор магазина должен указать почтовый индекс магазина в графе
					«Система» → «Настройки» → «Продажи» → «Доставка:
					общие настройки»→ «Расположение магазина» → «Почтовый индекс».'
				);
				df_assert($rmRequest->getDestinationPostalCode(), 'Укажите почтовый индекс.');
			}
			catch(Exception $e) {
				$result = Df_Shipping_Model_Rate_Result_Error::i($this, rm_ets($e));
			}
		}
		return $result;
	}
}