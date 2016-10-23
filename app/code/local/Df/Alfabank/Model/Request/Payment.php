<?php
/**
 * Обратите внимание, что платёжный шлюз Альфа-Банка (@see Df_Alfabank_Method)
 * не нуждается в получении параметров при перенаправлении на него покупателя.
 * Вместо этого модуль Альфа-Банк передаёт эти параметры предварительным запросом
 * @see Df_Alfabank_Method::getRegistrationResponse()
 * и платёжный шлюз возвращает модулю уникальный веб-адрес
 * @see Df_Alfabank_Method::getPaymentPageUrl()
 * на который модуль перенаправляет покупателя без параметров.
 */
class Df_Alfabank_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/**
	 * @override
	 * @see Df_Payment_Model_Request_Payment::_params()
	 * @used-by Df_Payment_Model_Request_Payment::params()
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