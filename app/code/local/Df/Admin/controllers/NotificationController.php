<?php
class Df_Admin_NotificationController extends Df_Core_Controller_Admin {
	/** @return void */
	public function deleteDemoStoreAction() {
		Df_Admin_Model_Action_DeleteDemoStore::i($this)->process();
	}
	/**
	 * @see Df_Admin_Model_Notifier::getUrlSkip()
	 * @return void
	 */
	public function skipAction() {Df_Admin_Model_Action_Notification_Skip::i($this)->process();}
}