<?php
class Df_Admin_Model_Action_Notification_Skip extends Df_Core_Model_Controller_Action_Admin {
	/**
	 * @override
	 * @return string
	 */
	protected function generateResponseBody() {
		try {
			Mage::getConfig()->saveConfig($this->getConfigPath(), 1);
			Mage::getConfig()->reinit();
		}
		catch (Exception $e) {
			rm_exception_to_session($e);
		}
		$this->getController()->redirectReferer();
		return '';
	}

	/** @return string */
	private function getConfigPath() {
		return Df_Admin_Model_Notifier::getConfigPathSkipByClass($this->getNotifierClass());
	}

	/** @return string */
	private function getNotifierClass() {
		return $this->getController()->getRequest()->getParam(self::RP__CLASS);
	}

	const RP__CLASS = 'class';
	/**
	 * @static
	 * @param Df_Admin_NotificationController $controller
	 * @return Df_Admin_Model_Action_Notification_Skip
	 */
	public static function i(Df_Admin_NotificationController $controller) {
		return new self(array(self::P__CONTROLLER => $controller));
	}
}