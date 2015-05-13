<?php
class Df_Cms_Admin_NotificationController extends Df_Core_Controller_Admin {
	/** @return void */
	public function deleteOrphanBlocksAction() {
		Df_Cms_Model_Admin_Action_DeleteOrphanBlocks::i($this)->process();
	}
}