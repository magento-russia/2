<?php
abstract class Df_Shipping_Exception_Request extends Df_Shipping_Exception {
	/**
	 * @override
	 * @param Exception $e
	 * @param Df_Shipping_Model_Request $request
	 */
	public function __construct(Exception $e, Df_Shipping_Model_Request $request) {
		parent::__construct();
		$this->_exception = $e;
		$this->_request = $request;
	}

	/** @return Exception */
	public function getException() {return $this->_exception;}

	/**
	 * @override
	 * @return string
	 */
	public function getMessageRm() {return df_ets($this->getException());}

	/** @return Df_Shipping_Model_Request */
	public function getRequest() {return $this->_request;}

	/** @var Exception */
	private $_exception;
	/** @var Df_Shipping_Model_Request */
	private $_request;
}