<?php
class Df_C1_Cml2_Action_Login extends Df_C1_Cml2_Action {
	/**
	 * @override
	 * @see Df_Core_Model_Action::_process()
	 * @used-by Df_Core_Model_Action::process()
	 * @return void
	 * @throws Exception
	 */
	protected function _process() {
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
			$this->sessionMagentoAPI()->start($sessionName = null);
			/** @var Mage_Api_Model_User $apiUser */
			$apiUser = null;
			try {
				$apiUser = $this->sessionMagentoAPI()->login($userName, $password);
			}
			catch (Exception $e) {
				df_error('Авторизация не удалась: неверно системное имя «%s», либо пароль к нему.', $userName);
			}
			if (!df_bool($apiUser->getIsActive())) {
				df_error('Администратор «%s» не допущен к работе', $userName);
			}
			if (!$this->sessionMagentoAPI()->isAllowed('rm/_1c')) {
				df_error(
					"Администратор «%s»
					не допущен к обмену данными между Magento и 1С:Управление торговлей.
					\nДля допуска администратора к данной работе
					наделите администратора должностью, которая обладает полномочием
					«Российская сборка» → «1С:Управление торговлей»."
					,$userName
				);
			}
			$this->setResponseLines(
				'success'
				, Df_C1_Cml2_Cookie::SESSION_ID
				, $this->sessionMagentoAPI()->getSessionId()
				, ''
			);
		}
		catch (Exception $e) {
			df_c1()->logRaw(df_ets($e));
			df_notify_exception($e);
			$this->response()->setHeader($name = 'HTTP/1.1', $value = '401 Unauthorized');
			throw $e;
		}
	}
}