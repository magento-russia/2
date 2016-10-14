<?php
class Df_Payment_RedirectController extends Mage_Core_Controller_Front_Action {
	/**
	 * Перенаправляет покупателя
	 * на внешнуюю для магазина платёжную страницу  платёжной системы
	 * @return void
	 */
	public function indexAction() {
		try {
			/**
			 * Если покупатель перешёл сюда с сайта платёжной системы,
			 * а не со страницы нашего магазина,
			 * то нужно не перенаправлять покупателя назад на сайт платёжной системы,
			 * а позволить покупателю оплатить заказ другим способом.
			 *
			 * Покупатель мог перейти сюда с сайта платёжной системы,
			 * нажав кнопку «Назад» в браузере,
			 * или же нажав специализированную кнопку отмены операции на сайте платёжной системы
			 * (например, на платёжной странице LiqPay кнопка «В магазин»
			 * работает как javascript:history.back()).
			 *
			 * Обратите внимание, что последние версии браузеров Firefox и Chrome
			 * при нажатии посетителем браузерной кнопки «Назад»
			 * перенаправляют посетилеля не на страницу df_payment/redirect,
			 * а сразу на страницу checkout/onepage.
			 *
			 * Впервые заметил такое поведение 17 сентября 2013 года в Forefox 23.0.1 и Chrome 29,
			 * причём Internet Explorer 10 в тот же день вёл себя по-прежнему.
			 *
			 * Видимо, Firefox и Chrome так делают по той причине,
			 * что посетитель со страницы checkout/onepage
			 * перенаправляется через страницу df_payment/redirect на страницу платёжной системы
			 * автоматически, скриптом, без участия покупателя.
			 *
			 * Поэтому мы делаем обработку в двух точках:
			 * @see Df_Payment_RedirectController::indexAction
			 * @see Df_Checkout_Observer::controller_action_predispatch_checkout
			 */
			if (Df_Payment_Redirected::is()) {
				Df_Payment_Redirected::restoreQuote();
				$this->_redirect(RM_URL_CHECKOUT);
			}
			else {
				Df_Payment_Redirected::on();
				$this->loadLayout();
				$this->renderLayout();
			}
		}
		catch (Exception $e) {
			/**
			 * Обратите внимание,
			 * что при возвращении на страницу RM_URL_CHECKOUT
			 * диагностическое сообщение надо добавлять в rm_session_core(),
			 * а не в rm_session_checkout(),
			 * потому что сообщения сессии checkout
			 * не отображаются в стандартной теме на странице checkout/onepage
			 */
			rm_exception_to_session($e);
			df_notify_exception($e);
			Df_Payment_Redirected::restoreQuote();
			$this->_redirect(RM_URL_CHECKOUT);
		}
	}
}