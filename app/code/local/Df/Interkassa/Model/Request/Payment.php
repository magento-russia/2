<?php
/** @method Df_Interkassa_Model_Payment getMethod() */
class Df_Interkassa_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/**
	 * @override
	 * @see Df_Payment_Model_Request_Payment::_params()
	 * @used-by Df_Payment_Model_Request_Payment::params()
	 * @return array(string => string|int)
	 */
	protected function _params() {
		return array(
			'ik_status_method' => 'POST'
			,'ik_success_method' => 'POST'
			,'ik_fail_method' => 'POST'
			,'ik_payment_amount' => $this->amountS()
			,'ik_payment_desc' => $this->getTransactionDescription()
			,'ik_payment_id' => $this->orderIId()
			,'ik_paysystem_alias' => ''
			,'ik_shop_id' => $this->shopId()
			,'ik_status_url' => $this->urlConfirm()
			,'ik_success_url' => df_url_checkout_success()
			,'ik_fail_url' => df_url_checkout_fail()
		);
	}
}