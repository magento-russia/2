<?php
class Df_Alfabank_Model_Request_State extends Df_Alfabank_Model_Request_Secondary {
	/**
	 * @override
	 * @return string
	 */
	protected function getGenericFailureMessageUniquePart() {
		return 'запросе состояния заказа в системе Альфа-Банка';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getResponseClass() {return Df_Alfabank_Model_Response_State::_CLASS;}

	/**
	 * @override
	 * @return string
	 */
	protected function getServiceName() {return 'getOrderStatus';}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Alfabank_Model_Request_State
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}


