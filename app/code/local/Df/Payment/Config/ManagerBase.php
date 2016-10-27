<?php
namespace Df\Payment\Config;
/** @method \Df\Payment\Method main() */
abstract class ManagerBase extends \Df\Checkout\Module\Config\Manager {
	/**
	 * @override
	 * @param string $key
	 * @return string
	 */
	protected function adaptKey($key) {return
		df_cc_path($this->getKeyBase(), $this->main()->getRmId(), $key)
	;}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__MAIN, \Df\Payment\Method::class);
	}
}