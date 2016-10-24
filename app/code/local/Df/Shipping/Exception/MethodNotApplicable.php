<?php
namespace Df\Shipping\Exception;
use \Df_Shipping_Carrier as Carrier;
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
	 * @override
	 * @return bool
	 */
	public function needNotifyAdmin() {return false;}

	/**
	 * @override
	 * @return bool
	 */
	public function needNotifyDeveloper() {return false;}

	/**
	 * 2016-10-24
	 * @override
	 * @see \Df\Shipping\Exception::carrier()
	 * @used-by \Df\Shipping\Exception::reportNamePrefix()
	 * @return \Df_Shipping_Carrier
	 */
	protected function carrier() {return $this->_carrier;}

	/**
	 * 2016-10-24
	 * @var Carrier
	 */
	private $_carrier;
}