<?php
class Df_Alfabank_Request_Capture extends Df_Alfabank_Request_Secondary {
	/**
	 * @override
	 * @see \Df\Payment\Request\Secondary::getGenericFailureMessageUniquePart()
	 * @used-by \Df\Payment\Request\Secondary::getGenericFailureMessage()
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