<?php
class Df_Masterbank_Model_Request_Capture extends Df_Masterbank_Model_Request_Secondary {
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
	protected function getServiceName() {
		return 'close';
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Masterbank_Model_Request_Capture
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}


