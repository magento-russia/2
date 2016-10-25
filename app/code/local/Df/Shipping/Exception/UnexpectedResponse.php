<?php
namespace Df\Shipping\Exception;
use \Df\Shipping\Request as Req;
use Df_Shipping_Response as Res;
use \Exception as E;
class UnexpectedResponse extends Request {
	/**
	 * @override
	 * @param E $e
	 * @param Req $request
	 * @param Res $response
	 */
	public function __construct(E $e, Req $request, Res $response) {
		parent::__construct($e, $request);
		$this->_response = $response;
	}

	/** @return Res */
	public function getResponse() {return $this->_response;}

	/** @var Res */
	private $_response;
}