<?php
namespace Df\Shipping\Exception;
use Df_Shipping_Model_Request as R;
use \Exception as E;
abstract class Request extends \Df\Shipping\Exception {
	/**
	 * @override
	 * @param E $e
	 * @param R $request
	 */
	public function __construct(E $e, R $request) {
		parent::__construct();
		$this->_exception = $e;
		$this->_request = $request;
	}

	/** @return E */
	public function getException() {return $this->_exception;}

	/**
	 * @override
	 * @see \Df\Shipping\Exception::message()
	 * @return string
	 */
	public function message() {return df_ets($this->getException());}

	/** @return R */
	public function getRequest() {return $this->_request;}

	/**
	 * 2016-10-24
	 * @override
	 * @see \Df\Shipping\Exception::carrier()
	 * @used-by \Df\Shipping\Exception::reportNamePrefix()
	 * @return \Df_Shipping_Carrier
	 */
	protected function carrier() {return $this->getRequest()->getCarrier();}

	/** @var E */
	private $_exception;
	/** @var R */
	private $_request;
}