<?php
class Df_Alfabank_CustomerReturnController extends Mage_Core_Controller_Front_Action {
	/**
	 * Платёжная система присылает сюда подтверждение приёма оплаты от покупателя.
	 * @return void
	 */
	public function indexAction() {Df_Alfabank_Model_Action_CustomerReturn::i($this)->process();}
}