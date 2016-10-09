<?php
class Df_MoySklad_IndexController extends Df_Core_Controller_Admin {
	/** @return void */
	public function indexAction() {
		try {
			rm_log(__METHOD__);
			$this->loadLayout();
			$this->renderLayout();
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}
}