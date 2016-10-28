<?php
namespace Df\Robokassa\Request;
use Df_Sales_Model_Order_Item_Extended as OIE;
use Mage_Sales_Model_Order_Item as OI;
/** @method \Df\Robokassa\Method method() */
class Payment extends \Df\Payment\Request\Payment {
	/**
	 * @override
	 * @see \Df\Payment\Request\Payment::_params()
	 * @used-by \Df\Payment\Request\Payment::params()
	 * @return array(string => string|int)
	 */
	protected function _params() {return $this->paramsBasic() + [
		// 2016-10-19
		// «E-Mail покупателя автоматически подставляется в платёжную форму ROBOKASSA.
		// Пользователь может изменить его в процессе оплаты.»
		// http://docs.robokassa.ru/#1202
		'Email' => $this->email()
		/**
		 * 2016-10-19
		 * «Кодировка, в которой отображается страница ROBOKASSA.
		 * По умолчанию: windows-1251.
		 * тот же параметр влияет на корректность отображения описания покупки (InvDesc)
		 * в интерфейсе ROBOKASSA, и на правильность передачи
		 * Дополнительных пользовательских параметров,
		 * если в их значениях присутствует язык отличный от английского.»
		 * http://docs.robokassa.ru/#1201
		 * В примере значение именно строчными буквами: http://docs.robokassa.ru/#1236
		 */
		,'Encoding' => 'utf-8'
		/**
		 * 2016-10-19
		 * «Описание покупки, можно использовать только символы английского или русского алфавита,
		 * цифры и знаки препинания. Максимальная длина — 100 символов.
		 * Эта информация отображается в интерфейсе ROBOKASSA и в Электронной квитанции,
		 * которую мы выдаём клиенту после успешного платежа.
		 * Корректность отображения зависит от необязательного параметра Encoding»
		 * http://docs.robokassa.ru/#1189
		 */
		,'InvDesc' => $this->description()
		// 2016-10-19
		// http://docs.robokassa.ru/#2387
		,'IsTest' => $this->configS()->isTestMode() ? 1 : 0
		// 2016-10-19
		// «Контрольная сумма»
		// http://docs.robokassa.ru/#1190
		,'SignatureValue' => $this->signature()
	];}

	/**
	 * @override
	 * @see \Df\Payment\Request\Payment::description()
	 * @return string
	 */
	protected function description() {return dfc($this, function() {return
		df_csv_pretty(df_map(function(OI $oi) {return
			sprintf('%s (%d)', $oi->getName(), OIE::i($oi)->getQtyOrdered())
		;}, $this->order()->getItemsCollection([], true)))
	;});}

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
		/**
		 * 2016-10-20
		 * Как выяснил сегодня в техподдержке ROBOKASSSA,
		 * у магазина нет возможности выставлять покупателю счёт не в рублях:
		 * https://partner.robokassa.ru/Support/Requests/29f54a98-5c8e-4608-85f6-748fb3a2cbf9
		 *
		 * При этом в API есть параметр «OutSumCurrency»: http://docs.robokassa.ru/#1204
		 * «Способ указать валюту, в которой магазин выставляет стоимость заказа.»
		 * Однако этот параметр на самом деле лишь позволяет указывать «OutSum» в нестандартной валюте,
		 * однако стоимость заказа будет всё равно пересчитана в рубли.
		 * http://magento-forum.ru/topic/1629/#entry20178
		 */
	];});}

	/** @return string */
	private function signature() {return md5(implode(':', $this->preprocessParams(
		$this->paramsBasic() + ['dummy-1' => $this->password()]
	)));}
}