<?php
class Df_Psbank_Model_Request_Capture extends Df_Psbank_Model_Request_Secondary {
	/**
	 * @override
	 * @return string
	 */
	protected function getGenericFailureMessageUniquePart() {
		return 'снятии ранее зарезервированных средств с карты покупателя';
	}
	/**
	 * @override
	 * @return int
	 */
	protected function getTransactionType() {return 21;}
}


