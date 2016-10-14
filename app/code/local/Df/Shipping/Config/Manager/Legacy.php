<?php
class Df_Shipping_Config_Manager_Legacy extends Df_Shipping_Config_Manager {
	/**
	 * @override
	 * @param string $key
	 * @return string
	 */
	protected function adaptKey($key) {
		return df_concat_xpath('carriers', $this->main()->getCarrierCode(), $key);
	}

	/**
	 * @override
	 * @see Df_Checkout_Module_Config_Manager::s()
	 * @used-by Df_Shipping_Config_Manager::legacy()
	 * @static
	 * @param Df_Checkout_Module_Main|Df_Shipping_Carrier $main
	 * @return Df_Shipping_Config_Manager_Legacy
	 */
	public static function s(Df_Checkout_Module_Main $main) {return self::sc(__CLASS__, $main);}
}