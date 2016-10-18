<?php
class Df_Payment_Config_Manager_Const_Default extends Df_Payment_Config_Manager_Const {
	/**
	 * @override
	 * @param string $key
	 * @return string
	 */
	protected function adaptKey($key) {
		return df_cc_path($this->getKeyBase(), 'default', parent::adaptKey($key));
	}

	/**
	 * @param Df_Payment_Model_Method|Df_Checkout_Module_Main $method
	 * 2016-10-18
	 * Тип параметра — всегда @see Df_Payment_Model_Method,
	 * но в сигнатуре вынуждены указать @see Df_Checkout_Module_Main
	 * для совместимости с унаследованным методом @see Df_Checkout_Module_Config_Manager::s()
	 * @return Df_Payment_Config_Manager_Const_Default
	 */
	public static function s(Df_Checkout_Module_Main $method) {return self::sc(__CLASS__, $method);}
}