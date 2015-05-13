<?php
class Df_Avangard_Model_Request_State extends Df_Avangard_Model_Request_Secondary {
	/**
	 * @override
	 * @return string
	 */
	protected function getGenericFailureMessageUniquePart() {
		return 'запросе состояния заказа в системе Банка Авангард';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getRequestDocumentTag() {
		return Df_Avangard_Model_RequestDocument::TAG__STATE;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getRequestUriSuffix() {
		return 'get_order_info';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getResponseClass() {
		return Df_Avangard_Model_Response_State::_CLASS;
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Avangard_Model_Request_State
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}