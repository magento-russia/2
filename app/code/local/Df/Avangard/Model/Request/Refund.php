<?php
class Df_Avangard_Model_Request_Refund extends Df_Avangard_Model_Request_Secondary {
	/**
	 * @override
	 * @return string
	 */
	protected function getGenericFailureMessageUniquePart() {
		return 'возврате оплаты покупателю';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getResponseClass() {
		return Df_Avangard_Model_Response_Refund::_CLASS;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getRequestDocumentTag() {
		return Df_Avangard_Model_RequestDocument::TAG__REFUND;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getRequestUriSuffix() {
		return 'reverse_order';
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Avangard_Model_Request_Refund
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}