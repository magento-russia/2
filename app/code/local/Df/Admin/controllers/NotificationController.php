<?php
class Df_Admin_NotificationController extends Df_Core_Controller_Admin {
	/**
	 * @uses Df_Admin_Model_Action_DeleteDemoStore
	 * @return void
	 */
	public function deleteDemoStoreAction() {rm_action($this, 'DeleteDemoStore');}

	/**
	 * @see Df_Admin_Model_Notifier::getUrlSkip()
	 * @uses Df_Admin_Model_Action_SkipNotification()
	 * @return void
	 */
	public function skipAction() {rm_action($this, 'SkipNotification');}
}