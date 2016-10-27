<?php
class Df_Payment_CancelController extends Mage_Core_Controller_Front_Action {
	/**
	 * Платёжная страница перенаправляет сюда покупателя,
	 * если по каким-то причинам оплата не состоялась.
	 * @return void
	 */
	public function indexAction() {
		\Df\Payment\Redirected::restoreQuote();
		if (!df_session_core()->getMessages()->getErrors()) {
			df_session_checkout()->addError('К сожалению, оплата заказа была прервана. Оформите Ваш заказ повторно.');
		}
		$this->_redirect('checkout/cart');
	}
}