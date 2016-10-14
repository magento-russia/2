<?php
/** @method Df_AccessControl_Model_Event_Permissions_Role_Saverole getEvent() */
class Df_AccessControl_Model_Handler_Permissions_Role_Saverole_UpdateCatalogAccessRights
	extends Df_Core_Model_Handler {
	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		if (df_cfg()->admin()->access_control()->getEnabled()) {
			/**
			 * true, если расширенное управление доступом
			 * включено для данной конкретной должности
			 */
			if ($this->getEvent()->isModuleEnabledForRole()) {
				$this->getRole()->setCategoryIds($this->getEvent()->getSelectedCategoryIds());
				if (is_null($this->getRole()->getId())) {
					$this->getRole()->prepareForInsert($this->getEvent()->getRoleId());
				}
				$this->getRole()
					->save()
					/**
					 * Небольшой хак.
					 * Если ранее вызывался метод prepareForInsert(), то id сбрасывается.
					 * Лень разбираться.
					 */
					->setId($this->getEvent()->getRoleId())
				;
				df_assert_eq(rm_nat0($this->getEvent()->getRoleId()), rm_nat0($this->getRole()->getId()));
			}
			else {
				if ($this->getRole()->isModuleEnabled()) {
					$this->getRole()
						->delete()
						->setId(null)
					;
				}
			}
		}
	}

	/** @return Df_AccessControl_Model_Role */
	private function getRole() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_AccessControl_Model_Role::i();
			if ($this->getEvent()->getRoleId()) {
				$this->{__METHOD__}->load($this->getEvent()->getRoleId());
			}
		}
		return $this->{__METHOD__};
	}

	/**
	 * Класс события (для валидации события)
	 * @override
	 * @return string
	 */
	protected function getEventClass() {return Df_AccessControl_Model_Event_Permissions_Role_Saverole::_C;}

	/** @used-by Df_AccessControl_Observer::controller_action_postdispatch_adminhtml_permissions_role_saverole() */
	const _C = __CLASS__;
}