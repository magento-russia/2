<?php
use Df_Sales_Model_Order_Item_Extended as OIE;
use Mage_Sales_Model_Order_Item as OI;
/** @method Df_Robokassa_Model_Payment getMethod() */
class Df_Robokassa_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/**
	 * @override
	 * @see Df_Payment_Model_Request_Payment::_params()
	 * @used-by Df_Payment_Model_Request_Payment::params()
	 * @return array(string => string|int)
	 */
	protected function _params() {return $this->paramsBasic() + [
		/**
		 * 2016-10-19
		 * «Описание покупки, можно использовать только символы английского или русского алфавита,
		 * цифры и знаки препинания. Максимальная длина — 100 символов.
		 * Эта информация отображается в интерфейсе ROBOKASSA и в Электронной квитанции,
		 * которую мы выдаём клиенту после успешного платежа.
		 * Корректность отображения зависит от необязательного параметра Encoding»
		 * http://docs.robokassa.ru/#1189
		 */
		'InvDesc' => $this->description()
		// 2016-10-19
		// http://docs.robokassa.ru/#2387
		,'IsTest' => $this->configS()->isTestMode() ? 1 : 0
		// 2016-10-19
		// «Контрольная сумма»
		// http://docs.robokassa.ru/#1190
		,'SignatureValue' => $this->signature()
		// 2016-10-19
		// «E-Mail покупателя автоматически подставляется в платёжную форму ROBOKASSA.
		// Пользователь может изменить его в процессе оплаты.»
		// http://docs.robokassa.ru/#1202
		,'Email' => null
	];}

	/**
	 * @override
	 * @see Df_Payment_Model_Request_Payment::description()
	 * @return string
	 */
	protected function description() {return dfc($this, function() {return
		df_csv_pretty(df_map(function(OI $oi) {return
			sprintf('%s (%d)', $oi->getName(), OIE::i($oi)->getQtyOrdered())
		;}, $this->order()->getItemsCollection([], true)))
	;});}

	/**
	 * 2016-10-19
	 * @return bool
	 */
	private function isRUB() {return 'RUB' === $this->configS()->getCurrencyCodeInServiceFormat();}

	/**
	 * 2016-10-19
	 * @return array(string => string)
	 */
	private function paramsBasic() {return dfc($this, function() {return [
		// 2016-10-19
		// «Идентификатор магазина в ROBOKASSA, который Вы придумали при создании магазина.»
		// http://docs.robokassa.ru/#1068
		'MerchantLogin' => $this->shopId()
		/**
		 * 2016-10-19
		 * «Требуемая к получению сумма (буквально — стоимость заказа, сделанного клиентом).
		 * Формат представления — число, разделитель — точка, например: 123.45.
		 * Сумма должна быть указана в рублях.»
		 * http://docs.robokassa.ru/#1188
		 * Также сумму можно указать в валюте OutSumCurrency, если этот параметр передан.
		 * http://docs.robokassa.ru/#1204
		 */
		,'OutSum' => $this->amountS()
		/**
		 * 2016-10-19
		 * «Номер счета в магазине.»
		 * «Может принимать значения от 1 до 2147483647 (231-1).»
		 * http://docs.robokassa.ru/#1194
		 * @todo Использовать @uses orderIId() здесь не вполне корректно,
		 * потому что ROBOKASSA обязательно ожидает целое число, причём в заданном диапазоне,
		 * а @uses orderIId() и не всегда возвращает число, и даже если возвращает число,
		 * то оно может быть слишком длинным.
		 */
		,'InvId' => $this->orderIId()
	] + ($this->isRUB() ? [] : [
		/**
		 * 2016-10-19
		 * «Способ указать валюту, в которой магазин выставляет стоимость заказа.»
		 * http://docs.robokassa.ru/#1204
		 * При этом значение RUB передавать нельзя.
		 */
		'OutSumCurrency' => $this->configS()->getCurrencyCodeInServiceFormat()
	]);});}

	/** @return string */
	private function signature() {return md5(implode(':', $this->preprocessParams(
		$this->paramsBasic() + ['dummy-1' => $this->password()]
	)));}
}