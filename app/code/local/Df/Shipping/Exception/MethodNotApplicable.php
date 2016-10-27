<?php
namespace Df\Shipping\Exception;
use Df\Shipping\Carrier as Carrier;
class MethodNotApplicable extends \Df\Shipping\Exception {
	/**
	 * 2016-10-24
	 * @param Carrier $carrier
	 * @param string $message
	 */
	public function __construct(Carrier $carrier, $message) {
		$this->_carrier = $carrier;
		parent::__construct($message);
	}

	/**
	 * 2016-10-25
	 * @override
	 * @see  \Df\Shipping\Exception::messageC()
	 * @return string
	 */
	public function messageC() {return $this->message();}

	/**
	 * 2016-10-24
	 * @override
	 * @see \Df\Shipping\Exception::carrier()
	 * @used-by \Df\Shipping\Exception::reportNamePrefix()
	 * @return \Df\Shipping\Carrier
	 */
	protected function carrier() {return $this->_carrier;}

	/**
	 * 2016-10-24
	 * @var Carrier
	 */
	private $_carrier;
}