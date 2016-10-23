<?php
class Df_Shipping_Setup_2_30_0 extends Df_Core_Setup {
	/**
	 * @override
	 * @see Df_Core_Setup::_process()
	 * @used-by Df_Core_Setup::process()
	 * @return void
	 */
	protected function _process() {
		/** @var array(string => int) $cacheOptions */
		$cacheOptions = Mage::app()->useCache();
		if (!dfa($cacheOptions, Df_Shipping_Model_Request::CACHE_TYPE)) {
			$cacheOptions[Df_Shipping_Model_Request::CACHE_TYPE] = 1;
			Mage::app()->saveUseCache($cacheOptions);
		}
	}
}