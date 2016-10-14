<?php
class Df_Kkb_Model_Request_Void extends Df_Kkb_Model_Request_Secondary {
	/**
	 * @override
	 * @return string
	 */
	protected function getGenericFailureMessageUniquePart() {return 'снятии блокировки средств';}

	/**
	 * @override
	 * @return string
	 */
	public function getTransactionType() {return Df_Kkb_Model_RequestDocument_Secondary::TRANSACTION__VOID;}
}


