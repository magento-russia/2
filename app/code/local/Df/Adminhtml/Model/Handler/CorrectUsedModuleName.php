<?php
/**
 * @method Df_Core_Model_Event_Controller_Action_Predispatch getEvent
 */
class Df_Adminhtml_Model_Handler_CorrectUsedModuleName extends Df_Core_Model_Handler {
	/**
	 * Метод-обработчик события
	 * @override
	 * @return void
	 */
	public function handle() {
		/**
		 * Некоторые контроллеры,
		 * которые не являются наследниками Mage_Adminhtml_Controller_Action,
		 * пытаются, тем не менее, авторизоваться в административной части.
		 * Без проверки на наследство от Mage_Adminhtml_Controller_Action
		 * это приводит к сбою:
				Call to undefined method Mage_Rss_CatalogController::getUsedModuleName()
			при выполнении кода
		 * 		('adminhtml' === $this->getController()->getUsedModuleName())
		 * Встретил такое в магазине atletica.baaton.com
		 */
		if (
				rm_loc()->isEnabled()
			&&
				($this->getController() instanceof Mage_Adminhtml_Controller_Action)
			&&
				('adminhtml' === $this->getController()->getUsedModuleName())
		) {
			$this->getController()->setUsedModuleName(
				Df_Adminhtml_Model_Translator_Controller::s()->getModuleName($this->getController())
			);
		}
	}

	/**
	 * Класс события (для валидации события)
	 * @override
	 * @return string
	 */
	protected function getEventClass() {return Df_Core_Model_Event_Controller_Action_Predispatch::_CLASS;}

	/** @return Mage_Adminhtml_Controller_Action */
	private function getController() {return $this->getEvent()->getController();}

	const _CLASS = __CLASS__;
}