<?php
class Df_LiqPay_ConfirmController extends Mage_Core_Controller_Front_Action {
	/**
	 * Платёжная система присылает сюда подтверждение приёма оплаты от покупателя.
	 * @return void
	 */
	public function indexAction() {
		Df_LiqPay_Model_Action_Confirm::i($this)->process();
	}
}