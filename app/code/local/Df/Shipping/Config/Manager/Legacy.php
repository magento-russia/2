<?php
namespace Df\Shipping\Config\Manager;
class Legacy extends \Df\Shipping\Config\Manager {
	/**
	 * @override
	 * @see \Df\Shipping\Config\Manager::adaptKey()
	 * @param string $key
	 * @return string
	 */
	protected function adaptKey($key) {return
		df_cc_path('carriers', $this->main()->getCarrierCode(), $key)
	;}

	/**
	 * @override
	 * @see \Df\Checkout\Module\Config\Manager::s()
	 * @used-by \Df\Shipping\Config\Manager::legacy()
	 * @static
	 * @param \Df\Checkout\Module\Main|\Df\Shipping\Carrier $main
	 * @return self
	 */
	public static function s(\Df\Checkout\Module\Main $main) {return self::sc(__CLASS__, $main);}
}