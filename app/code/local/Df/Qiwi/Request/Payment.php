<?php
/** @method Df_Qiwi_Method method() */
class Df_Qiwi_Request_Payment extends \Df\Payment\Request\Payment {
	/**
	 * @override
	 * @see \Df\Payment\Request\Payment::_params()
	 * @used-by \Df\Payment\Request\Payment::params()
	 * @return array(string => string|int)
	 */
	protected function _params() {return [
		'to' => $this->qPhone()
		,'summ' => $this->amountS()
		,'currency' => $this->currencyCode()
		,'lifetime' => 24 * 45
		,'txn_id' => $this->orderIId()
		,'com' => $this->description()
		,'from'=> $this->shopId()
		,'check_agt' => 0
	];}

	/** @return string */
	private function qPhone() {
		/** @var string $result */
		$result = $this->method()->qPhone();
		df_assert_eq(10, strlen($result));
		df_result_string($result);
		return $result;
	}
}