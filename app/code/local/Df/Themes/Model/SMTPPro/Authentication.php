<?php
// 2016-11-16
class Df_Themes_Model_SMTPPro_Authentication extends Aschroder_SMTPPro_Model_System_Config_Source_Smtp_Authentication {
	/**
	 * 2016-11-16
	 * Как настроить модуль «SMTP Pro» для Mailgun? http://magento-forum.ru/topic/5500/
	 * @override
	 * @see Aschroder_SMTPPro_Model_System_Config_Source_Smtp_Authentication::toOptionArray()
	 * @return array(string => string)
	 */
	public function toOptionArray() {return
		array('login' => 'логин и пароль') + parent::toOptionArray()
	;}
}