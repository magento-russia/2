<?php
namespace Df\Shipping;
/** @method \Df\Shipping\Carrier main() */
class Bridge extends \Df\Checkout\Module\Bridge {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__MAIN, Carrier::class);
	}
}