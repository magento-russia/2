<?php
class Df_Alfabank_Request_Refund extends Df_Alfabank_Request_Secondary {
	/**
	 * @override
	 * @return string
	 */
	protected function getGenericFailureMessageUniquePart() {return 'возврате оплаты покупателю';}

	/**
	 * @override
	 * @used-by Df_Alfabank_Request_Secondary::getUri()
	 * @return string
	 */
	protected function getServiceName() {return 'refund';}
}