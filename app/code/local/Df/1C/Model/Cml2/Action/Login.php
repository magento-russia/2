<?php
class Df_1C_Model_Cml2_Action_Login extends Df_1C_Model_Cml2_Action {
	/**
	 * @override
	 * @return void
	 * @throws Exception
	 */
	protected function processInternal() {
		try {
			/** @var string $userName */
			/** @var string $password */
			list($userName, $password) = df_mage()->core()->httpHelper()->authValidate();
			if (!$userName) {
				df_error('Администратор пытается авторизоваться с пустым системным именем, что недопустимо.');
			}
			if (!$password) {
				df_error(
					'Администратор «%s» пытается авторизоваться с пустым паролем, что недопустимо.'
					, $userName
				);
			}
			$this->getSessionMagentoAPI()->start($sessionName = null);
			/** @var Mage_Api_Model_User $apiUser */
			$apiUser = null;
			try {
				$apiUser = $this->getSessionMagentoAPI()->login($userName, $password);
			}
			catch (Exception $e) {
				df_error('Авторизация не удалась: неверно системное имя «%s», либо пароль к нему.', $userName);
			}
			if (!rm_bool($apiUser->getIsActive())) {
				df_error('Администратор «%s» не допущен к работе', $userName);
			}
			if (!$this->getSessionMagentoAPI()->isAllowed('rm/_1c')) {
				df_error(
					"Администратор «%s»
					не допущен к обмену данными между Magento и 1С: Управление торговлей.
					\nДля допуска администратора к данной работе
					наделите администратора должностью, которая обладает полномочием
					«Российская сборка» → «1С: Управление торговлей»."
					,$userName
				);
			}
			$this->setResponseBodyAsArrayOfStrings(array(
				'success'
				,Df_1C_Model_Cml2_Cookie::SESSION_ID
				,$this->getSessionMagentoAPI()->getSessionId()
				,''
			));
		}
		catch(Exception $e) {
			df_h()->_1c()->logRaw(rm_ets($e));
			df_notify_exception($e);
			$this->getResponse()->setHeader($name = 'HTTP/1.1', $value = '401 Unauthorized');
			throw $e;
		}
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_1C_Model_Cml2_Action_Login
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}