<?php
class Df_IPay_ConfirmPaymentByShopController extends Mage_Core_Controller_Front_Action {
	/** @return void */
	public function indexAction() {
		Df_IPay_Model_Action_ConfirmPaymentByShop::i($this)->process();
	}
}