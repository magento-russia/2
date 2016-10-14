<?php
class Df_Kkb_Model_Request_Capture extends Df_Kkb_Model_Request_Secondary {
	/**
	 * @override
	 * @return string
	 */
	protected function getGenericFailureMessageUniquePart() {
		return 'снятии ранее зарезервированных средств с карты покупателя';
	}

	/**
	 * @override
	 * @return string
	 */
	public function getTransactionType() {
		return Df_Kkb_Model_RequestDocument_Secondary::TRANSACTION__CAPTURE;
	}
}


