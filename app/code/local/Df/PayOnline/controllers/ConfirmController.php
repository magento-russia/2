<?php
class Df_PayOnline_ConfirmController extends Mage_Core_Controller_Front_Action {
	/**
	 * Платёжная система присылает сюда подтверждение приёма оплаты от покупателя.
	 * @return void
	 */
	public function indexAction() {
		Df_PayOnline_Model_Action_Confirm::i($this)->process();
	}
}