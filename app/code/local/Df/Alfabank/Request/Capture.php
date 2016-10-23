<?php
class Df_Alfabank_Request_Capture extends Df_Alfabank_Request_Secondary {
	/**
	 * @override
	 * @see Df_Payment_Request_Secondary::getGenericFailureMessageUniquePart()
	 * @used-by Df_Payment_Request_Secondary::getGenericFailureMessage()
	 * @return string
	 */
	protected function getGenericFailureMessageUniquePart() {
		return 'снятии ранее зарезервированных средств с карты покупателя';
	}

	/**
	 * @override
	 * @see Df_Alfabank_Request_Secondary::getServiceName()
	 * @used-by Df_Alfabank_Request_Secondary::getUri()
	 * @return string
	 */
	protected function getServiceName() {return 'deposit';}
}