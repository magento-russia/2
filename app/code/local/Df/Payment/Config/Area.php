<?php
/**
 * @method Df_Payment_Model_Method main()
 * @method Df_Payment_Config_Manager manager()
 */
abstract class Df_Payment_Config_Area extends Df_Checkout_Module_Config_Area {
	/**
	 * @param string $key
	 * @param bool $canBeTest [optional]
	 * @param string $default [optional]
	 * @return string
	 */
	public function getConst($key, $canBeTest = true, $default = '') {
		return $this->constManager()->getConst($key, $canBeTest, $default);
	}

	/**
	 * Получаем заданное ранее администратором
	 * значение конкретной настройки платёжного способа
	 * @override
	 * @param string $key
	 * @param string $default [optional]
	 * @return string
	 */
	public function getVarWithDefaultConst($key, $default = '') {
		return $this->getVar(
			$key, $this->constManager()->getValue($this->getAreaPrefix(), $key, $default)
		);
	}

	/**
	 * @used-by getConst()
	 * @used-by Df_Assist_Model_Config_Area_Service::getUrl()
	 * @used-by Df_IPay_Model_Config_Area_Service
	 * @used-by Df_Payment_Config_Area_Service
	 * @used-by Df_PayOnline_Model_Config_Area_Service
	 * @return Df_Payment_Config_Manager_Const
	 */
	protected function constManager() {return $this->main()->constManager();}
}