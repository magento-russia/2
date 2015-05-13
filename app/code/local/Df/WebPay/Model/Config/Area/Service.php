<?php
class Df_WebPay_Model_Config_Area_Service extends Df_Payment_Model_Config_Area_Service {
	/**
	 *  Использовать ли промышленный платёжный сервис WEBPAY в тестовом режиме?
		Укажите в данном поле значение «да»,
	 	если компания WEBPAY уже предоставила Вам доступ
		к промышленному платёжному сервису,
	 	однако Вы хотите, чтобы платежи проводились в тестовом режиме.
		В тестовом режиме денежные средства с покупателя не списываются.
	 * @return bool
	 */
	public function isTestModeOnProduction() {
		return $this->getVarFlag(self::KEY__VAR__TEST_ON_PRODUCTION);
	}
	const KEY__VAR__TEST_ON_PRODUCTION = 'test_on_production';
}