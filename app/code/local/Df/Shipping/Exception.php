<?php
namespace Df\Shipping;
abstract class Exception extends \Df\Core\Exception {
	/**
	 * 2016-10-24
	 * @used-by \Df\Shipping\Exception::reportNamePrefix()
	 * @return \Df_Shipping_Carrier
	 */
	abstract protected function carrier();

	/**
	 * 2016-10-24
	 * @override
	 * @see \Df\Core\Exception::reportNamePrefix()
	 * @used-by \Df\Qa\Message\Failure\Exception::reportNamePrefix()
	 * @return string|string[]
	 */
	public function reportNamePrefix() {return [df_module_name_lc($this->carrier()), 'exception'];}
}