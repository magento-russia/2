<?php
class Df_Shipping_Model_Setup_2_30_0 extends Df_Core_Model_Setup {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		/** @var array(string => int) $cacheOptions */
		$cacheOptions = Mage::app()->useCache();
		if (!df_a($cacheOptions, Df_Shipping_Model_Request::CACHE_TYPE)) {
			$cacheOptions[Df_Shipping_Model_Request::CACHE_TYPE] = 1;
			Mage::app()->saveUseCache($cacheOptions);
		}
	}

	/** @return Df_Shipping_Model_Setup_2_30_0 */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}