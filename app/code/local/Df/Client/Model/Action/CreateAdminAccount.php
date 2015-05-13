<?php
/**
 * @method Dfa_Server_Model_Message_Request_CreateAdminAccount getMessageRequest()
 */
class Df_Client_Model_Action_CreateAdminAccount extends Df_Core_Model_RemoteControl_Action_Concrete {
	/**
	 * @override
	 * @return Df_Client_Model_Action_CreateAdminAccount
	 */
	public function process() {
		/** @var bool|array $errors */
		$errors = $this->getAdmin()->validate();
		if (true !== $errors) {
			df_error(implode(Df_Core_Const::T_NEW_LINE, $errors));
		}
		$this->getAdmin()->save();
		$this->getAdmin()->setData(Df_Admin_Model_User::P__ROLE_ID, 1);
		$this->getAdmin()->add();
		return $this;
	}

	/**
	 * @override
	 * @return Df_Client_Model_Message_Response_CreateAdminAccount
	 */
	protected function createMessageResponse() {
		return
			Df_Client_Model_Message_Response_CreateAdminAccount::i(
				array(
					Df_Client_Model_Message_Response_CreateAdminAccount::P__IS_OK => true
					,Df_Client_Model_Message_Response_CreateAdminAccount::P__TEXT => 'OK'
					,Df_Client_Model_Message_Response_CreateAdminAccount
						::P__URL__ADMIN => df_h()->admin()->getAdminUrl()
				)
			)
		;
	}

	/** @return Df_Admin_Model_User */
	private function getAdmin() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Admin_Model_User $result */
			$result = Df_Admin_Model_User::i();
			$result->loadByUsername($this->getMessageRequest()->getAdminName());
			$result->addData(array(
				Df_Admin_Model_User::P__EMAIL => $this->getEmailRandom()
				,Df_Admin_Model_User::P__FIRSTNAME => 'Техническая'
				,Df_Admin_Model_User::P__LASTNAME => 'Поддержка'
				,Df_Admin_Model_User::P__NEW_PASSWORD => $this->getMessageRequest()->getAdminPassword()
				,Df_Admin_Model_User::P__PASSWORD_CONFIRMATION =>
					$this->getMessageRequest()->getAdminPassword()
				,Df_Admin_Model_User::P__USERNAME => $this->getMessageRequest()->getAdminName()
				,Df_Admin_Model_User::P__IS_ACTIVE => true
			));
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getEmailRandom() {return implode('@', array(rm_uniqid(), 'example.ru'));}

	const _CLASS = __CLASS__;
}