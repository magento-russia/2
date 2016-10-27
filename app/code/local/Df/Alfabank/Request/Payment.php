<?php
namespace Df\Alfabank\Request;
/**
 * Обратите внимание, что платёжный шлюз Альфа-Банка (@see \Df\Alfabank\Method)
 * не нуждается в получении параметров при перенаправлении на него покупателя.
 * Вместо этого модуль Альфа-Банк передаёт эти параметры предварительным запросом
 * @see \Df\Alfabank\Method::getRegistrationResponse()
 * и платёжный шлюз возвращает модулю уникальный веб-адрес
 * @see \Df\Alfabank\Method::getPaymentPageUrl()
 * на который модуль перенаправляет покупателя без параметров.
 */
class Payment extends \Df\Payment\Request\Payment {
	/**
	 * @override
	 * @see \Df\Payment\Request\Payment::_params()
	 * @used-by \Df\Payment\Request\Payment::params()
	 * @return array(string => string|int)
	 */
	protected function _params() {return array(
		'amount' => df_round(100 * $this->amountF())
		,'currency' => 810
		,'orderNumber' => $this->orderIId()
		,'password' => $this->password()
		,'returnUrl' => $this->urlCustomerReturn()
		,'userName' => $this->shopId()
	);}
}