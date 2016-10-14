<?php
class Df_Avangard_Model_Request_Refund extends Df_Avangard_Model_Request_Secondary {
	/**
	 * @override
	 * @return string
	 */
	protected function getGenericFailureMessageUniquePart() {return 'возврате оплаты покупателю';}

	/**
	 * @override
	 * @return string
	 */
	protected function getRequestId() {return 'reverse_order';}
}