<?php
class Df_Avangard_CustomerReturnController extends Mage_Core_Controller_Front_Action {
	/**
	 * Платёжная система присылает сюда подтверждение приёма оплаты от покупателя.
	 * @return void
	 */
	public function indexAction() {
		Df_Avangard_Model_Action_CustomerReturn::i($this)->process();
	}
}