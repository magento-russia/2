<?php
class Df_Checkout_Module_Config_Area_No extends Df_Checkout_Module_Config_Area {
	/**
	 * @override
	 * @return string
	 */
	protected function getAreaPrefix() {return '';}

	/**
	 * @used-by Df_Checkout_Module_Config_Facade::area()
	 * @param Df_Checkout_Module_Main $main
	 * @return Df_Checkout_Module_Config_Area_No
	 */
	public static function s(Df_Checkout_Module_Main $main) {
		/** @var array(string => Df_Checkout_Module_Config_Area_No) $cache */
		static $cache;
		/** @var string $key */
		$key = $main->getCheckoutModuleType();
		if (!isset($cache[$key])) {
			$cache[$key] = self::ic(__CLASS__, $main);
		}
		return $cache[$key];
	}
}


