<?php
class Df_Pd4_IndexController extends Mage_Core_Controller_Front_Action {
	/** @return void */
	public function indexAction() {
		try {
			$this->loadLayout();
			rm_block_head()->setTitle(
				'Форма ПД-4 для заказа №'
				. df_h()->pd4()->getDocumentViewAction()->order()->getIncrementId()
			);
			$this->renderLayout();
		}
		catch (Exception $e) {
			df_notify_exception($e);
			rm_exception_to_session($e);
			$this->_redirect('');
		}
	}
}