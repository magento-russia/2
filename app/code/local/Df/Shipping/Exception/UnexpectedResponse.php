<?php
class Df_Shipping_Exception_UnexpectedResponse extends Df_Shipping_Exception_Request {
	/**
	 * @override
	 * @param Exception $e
	 * @param Df_Shipping_Model_Request $request
	 * @param Df_Shipping_Model_Response $response
	 */
	public function __construct(
		Exception $e, Df_Shipping_Model_Request $request, Df_Shipping_Model_Response $response
	) {
		parent::__construct($e, $request);
		$this->_response = $response;
	}

	/** @return Df_Shipping_Model_Response */
	public function getResponse() {return $this->_response;}

	/** @var Df_Shipping_Model_Response */
	private $_response;
}