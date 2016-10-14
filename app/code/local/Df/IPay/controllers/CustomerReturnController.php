<?php
class Df_IPay_CustomerReturnController extends Mage_Core_Controller_Front_Action {
	/** @return void */
	public function indexAction() {
		/** @var bool $success */
		try {
			$success = Mage_Sales_Model_Order::STATE_PROCESSING === df_last_order()->getState();
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e, false);
			$success = false;
		}
		$this->getResponse()->setRedirect($success ? rm_url_checkout_success() : rm_url_checkout_fail());
	}
}