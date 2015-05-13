<?php
class Df_Masterbank_Model_Request_Void extends Df_Masterbank_Model_Request_Secondary {
	/**
	 * @override
	 * @return string
	 */
	protected function getGenericFailureMessageUniquePart() {
		return 'снятии блокировки средств';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getServiceName() {
		return 'rollback';
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Masterbank_Model_Request_Void
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}


