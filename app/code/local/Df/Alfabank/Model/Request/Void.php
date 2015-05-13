<?php
class Df_Alfabank_Model_Request_Void extends Df_Alfabank_Model_Request_Secondary {
	/**
	 * @override
	 * @return string
	 */
	protected function getGenericFailureMessageUniquePart() {return 'снятии блокировки средств';}

	/**
	 * @override
	 * @return string
	 */
	protected function getResponseClass() {return Df_Alfabank_Model_Response_Void::_CLASS;}

	/**
	 * @override
	 * @return string
	 */
	protected function getServiceName() {return 'reverse';}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Alfabank_Model_Request_Void
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}


