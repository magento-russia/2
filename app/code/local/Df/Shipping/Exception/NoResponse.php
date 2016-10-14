<?php
/** @used-by Df_Shipping_Model_Request::getResponseAsText() */
class Df_Shipping_Exception_NoResponse extends Df_Shipping_Exception_Request {
	/**
	 * @override
	 * @param Exception $e
	 * @param Df_Shipping_Model_Request $request
	 * @return Df_Shipping_Exception_NoResponse
	 */
	public function __construct(Exception $e, Df_Shipping_Model_Request $request) {
		parent::__construct($e, $request);
	}
}