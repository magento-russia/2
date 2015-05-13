<?php
/**
 * @method Df_Admin_Model_Event_Roles_Save_After getEvent()
 */
class Df_AccessControl_Model_Handler_RemindLastSavedRoleId extends Df_Core_Model_Handler {
	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		if (
				df_enabled(Df_Core_Feature::ACCESS_CONTROL)
			&&
				df_cfg()->admin()->access_control()->getEnabled()
		) {
			df_h()->accessControl()
				->setLastSavedRoleId(
					$this->getEvent()->getRole()->getId()
				)
			;
		}

	}

	/**
	 * Класс события (для валидации события) @override
	 * @return string
	 */
	protected function getEventClass() {
		return Df_Admin_Model_Event_Roles_Save_After::_CLASS;
	}

	const _CLASS = __CLASS__;
}