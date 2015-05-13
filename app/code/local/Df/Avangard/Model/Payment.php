<?php
/**
 * @method Df_Avangard_Model_Request_Payment getRequestPayment()
 */
class Df_Avangard_Model_Payment extends Df_Payment_Model_Method_WithRedirect {
	/**
	 * Результат метода говорит системе о том, поддерживает ли способ оплаты
	 * автоматизированный возврат оплаты покупателю
	 * @override
	 * @return bool
	 */
	public function canRefund() {return true;}
	/**
	 * @override
	 * @return array(string => string)
	 */
	public function getPaymentPageParams() {
		return array('ticket' => $this->getRequestPayment()->getResponse()->getPaymentExternalId());
	}
	/**
	 * @override
	 * @return string
	 */
	public function getTemplateSuccess() {return 'df/avangard/success.phtml';}
}