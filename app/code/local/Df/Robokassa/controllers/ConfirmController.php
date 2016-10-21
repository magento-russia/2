<?php
// Платёжная система присылает сюда подтверждение приёма оплаты от покупателя.
class Df_Robokassa_ConfirmController extends Mage_Core_Controller_Front_Action {
	/** @return void */
	public function indexAction() {df_action($this);}
}