<?php
class Df_Payment_CancelController extends Mage_Core_Controller_Front_Action {
	/**
	 * Платёжная страница перенаправляет сюда покупателя,
	 * если по каким-то причинам оплата не состоялась.
	 * @return void
	 */
	public function indexAction() {
		Df_Payment_Model_Redirector::s()->restoreQuote();
		if (!rm_session_core()->getMessages()->getErrors()) {
			rm_session_checkout()->addError('К сожалению, оплата заказа была прервана. Оформите Ваш заказ повторно.');
		}
		$this->_redirect(Df_Checkout_Const::URL__CART);
	}
}