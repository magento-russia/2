<?php
namespace Df\Core\Exception;
use \Df\Core\Request as R;
use \Exception as E;
class Request extends \Df\Core\Exception {
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

	/** @return R */
	public function getRequest() {return $this->_request;}

	/**
	 * @override
	 * @see \Df\Shipping\Exception::message()
	 * @return string
	 */
	public function message() {return df_ets($this->getException());}

	/**
	 * 2016-10-24
	 * @override
	 * @see \Df\Core\Exception::reportNamePrefix()
	 * @used-by \Df\Qa\Message\Failure\Exception::reportNamePrefix()
	 * @return string|string[]
	 */
	public function reportNamePrefix() {return [
		df_module_name_lc($this->getRequest()), 'exception'
	];}

	/** @var E */
	private $_exception;
	/** @var R */
	private $_request;
}