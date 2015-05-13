<?php
class Df_Avangard_Model_Response_Refund extends Df_Avangard_Model_Response {
	/**
	 * @override
	 * @return array(string => string)
	 */
	public function getReportAsArray() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_clean(array(
				'Успешен ли запрос' => $this->isSuccessful() ? 'да' : 'нет'
				,'Диагностическое сообщение' => $this->onFail($this->getErrorMessage())
			));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	public function getTransactionType() {
		return Mage_Sales_Model_Order_Payment_Transaction::TYPE_REFUND;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getErrorMessage() {
		return
			(303 !== $this->getResponseCode())
			? parent::getErrorMessage()
			: rm_sprintf(
				'Не удалось вернуть оплату.'
				. '<br/>Обратите внимание, что платёжный шлюз Банка Авангард'
				. ' неспособен возвращать оплату в тестовом режиме.'
				. '<br/>Сообщение платёжного шлюза: «%s».'
				, df_trim(parent::getErrorMessage(), '.')
			)
		;
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Avangard_Model_Response_Refund
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}