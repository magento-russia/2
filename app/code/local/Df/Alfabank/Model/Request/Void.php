<?php
class Df_Alfabank_Model_Request_Void extends Df_Alfabank_Model_Request_Secondary {
	/**
	 * @override
	 * @return string
	 */
	protected function getGenericFailureMessageUniquePart() {return 'снятии блокировки средств';}

	/**
	 * @override
	 * @used-by Df_Alfabank_Model_Request_Secondary::getUri()
	 * @return string
	 */
	protected function getServiceName() {return 'reverse';}
}


