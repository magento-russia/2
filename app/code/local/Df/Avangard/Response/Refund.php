<?php
namespace Df\Avangard\Response;
use Mage_Sales_Model_Order_Payment_Transaction as T;
class Refund extends \Df\Avangard\Response {
	/**
	 * @override
	 * @return array(string => string)
	 */
	public function getReportAsArray() {return dfc($this, function() {return array_filter([
		'Успешен ли запрос' => $this->isSuccessful() ? 'да' : 'нет'
		,'Диагностическое сообщение' => $this->onFail($this->getErrorMessage())
	]);});}

	/**
	 * @override
	 * @return string
	 */
	public function getTransactionType() {return T::TYPE_REFUND;}

	/**
	 * @override
	 * @return string
	 */
	protected function getErrorMessage() {return
		303 !== $this->getResponseCode() ? parent::getErrorMessage() : df_sprintf(
			'Не удалось вернуть оплату.'
			. '<br/>Обратите внимание, что платёжный шлюз Банка Авангард'
			. ' неспособен возвращать оплату в тестовом режиме.'
			. '<br/>Сообщение платёжного шлюза: «%s».'
			, df_trim(parent::getErrorMessage(), '.')
		)
	;}
}