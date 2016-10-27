<?php
use Df\Shipping\Request as R;
class Df_Shipping_Setup_2_30_0 extends Df_Core_Setup {
	/**
	 * @override
	 * @see Df_Core_Setup::_process()
	 * @used-by Df_Core_Setup::process()
	 * @return void
	 */
	protected function _process() {
		/** @var array(string => int) $o */
		$o = Mage::app()->useCache();
		if (!dfa($o, R::CACHE_TYPE)) {
			$o[R::CACHE_TYPE] = 1;
			Mage::app()->saveUseCache($o);
		}
	}
}