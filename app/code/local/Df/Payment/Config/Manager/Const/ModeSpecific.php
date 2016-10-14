<?php
class Df_Payment_Config_Manager_Const_ModeSpecific extends Df_Payment_Config_Manager_Const {
	/**
	 * @param string $key
	 * @return string
	 */
	protected function adaptKey($key) {
		return df_cc_path($this->main()->isTestMode() ? 'test' : 'production', parent::adaptKey($key));
	}

	/**
	 * @param Df_Payment_Model_Method $method
	 * @return Df_Payment_Config_Manager_Const_ModeSpecific
	 */
	public static function s(Df_Payment_Model_Method $method) {return self::sc(__CLASS__, $method);}
}