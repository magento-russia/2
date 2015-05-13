<?php
class Df_Psbank_CustomerReturnController extends Mage_Core_Controller_Front_Action {
	/**
	 * Платёжная система возвращает сюда покупателя вне зависимости от успешности оплаты заказа.
	 * Наша задача — перенаправить покупателя:
	 * на страницу checkout/onepage/success в случае успешной оплаты
	 * на страницу checkout/onepage в случае неуспешной оплаты
	 * @return void
	 */
	public function indexAction() {Df_Psbank_Model_Action_CustomerReturn::i($this)->process();}
}