<?php
class Df_Checkout_Model_Dispatcher {
	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function checkout_type_multishipping_create_orders_single(
		Varien_Event_Observer $observer
	) {
		try {
			df_handle_event(
				Df_Checkout_Model_Handler_SaveOrderComment::_CLASS
				,Df_Checkout_Model_Event_CheckoutTypeMultishipping_CreateOrdersSingle::_CLASS
				,$observer
			);
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function checkout_type_onepage_save_order(
		Varien_Event_Observer $observer
	) {
		try {
			df_handle_event(
				Df_Checkout_Model_Handler_SaveOrderComment::_CLASS
				,Df_Checkout_Model_Event_CheckoutTypeOnepage_SaveOrder::_CLASS
				,$observer
			);
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function checkout_type_onepage_save_order_after(
		Varien_Event_Observer $observer
	) {
		try {
			df_handle_event(
				Df_Checkout_Model_Handler_SendGeneratedPasswordToTheCustomer::_CLASS
				,Df_Checkout_Model_Event_CheckoutTypeOnepage_SaveOrderAfter::_CLASS
				,$observer
			);
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function controller_action_predispatch_checkout(
		Varien_Event_Observer $observer
	) {
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
			 * @see Df_Checkout_Model_Dispatcher::controller_action_predispatch_checkout
			 */
			if (Df_Payment_Model_Redirector::s()->isRedirected()) {
				/** @var Mage_Core_Controller_Varien_Action $controller */
				$controller = $observer->getData('controller_action');
				if ('checkout_onepage_success' === $controller->getFullActionName()) {
					/**
					 * В отличие от метода
					 * @see Df_Payment_Model_Action_Confirm::process()
					 * здесь необходимость вызова unsetRedirected() не вызывает сомнений,
					 * потому что Df_Checkout_Model_Dispatcher:controller_action_predispatch_checkout()
					 * обрабатывает именно сессию покупателя, а не запрос платёжной системы
					 */
					Df_Payment_Model_Redirector::s()->unsetRedirected();
				}
				else {
					Df_Payment_Model_Redirector::s()->restoreQuote();
				}
			}
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}
}