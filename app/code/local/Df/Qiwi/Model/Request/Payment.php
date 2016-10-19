<?php
/** @method Df_Qiwi_Model_Payment getMethod() */
class Df_Qiwi_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/**
	 * @override
	 * @see Df_Payment_Model_Request_Payment::_params()
	 * @used-by Df_Payment_Model_Request_Payment::params()
	 * @return array(string => string|int)
	 */
	protected function _params() {
		return array(
			'to' => $this->getQiwiCustomerPhone()
			,'summ' => $this->amountS()
			,'currency' => $this->currencyCode()
			,'lifetime' => 24 * 45
			,'txn_id' => $this->orderIId()
			,'com' => $this->description()
			,'from'=> $this->shopId()
			,'check_agt' => 0
		);
	}

	/** @return string */
	private function getQiwiCustomerPhone() {
		/** @var string $result */
		$result = $this->getMethod()->getQiwiCustomerPhone();
		df_assert_eq(10, strlen($result));
		df_result_string($result);
		return $result;
	}
}