<?php
class Df_Payment_Redirected extends Df_Core_Model {
	/**
	 * @used-by Df_Checkout_Observer::controller_action_predispatch_checkout()
	 * @used-by Df_Payment_CancelController::indexAction()
	 * @used-by Df_Payment_RedirectController::indexAction()
	 * @return void
	 */
	public static function restoreQuote() {
		self::cancelOrderIfExists();
		self::restoreQuoteIfExists();
		self::off();
	}

	/**
	 * Флаг Df_Payment_Redirected::SESSION_PARAM__REDIRECTED_TO_PAYMENT_SYSTEM
	 * предназначен для отслеживания возвращения покупателя
	 * с сайта платёжной системы без оплаты.
	 * Если этот флаг установлен — значит, покупатель был перенаправлен
	 * на сайт платёжной системы.
	 * @used-by Df_Checkout_Observer::controller_action_predispatch_checkout()
	 * @used-by Df_Payment_RedirectController::indexAction()
	 * @return bool
	 */
	public static function is() {return rm_bool(self::session()->getData(self::$REDIRECTED));}

	/**
	 * @used-by Df_Alfabank_Model_Action_CustomerReturn::_process()
	 * @used-by Df_Avangard_Model_Action_CustomerReturn::_process()
	 * @used-by Df_Checkout_Observer::controller_action_predispatch_checkout()
	 * @used-by Df_Payment_Model_Action_Confirm::_process()
	 * @used-by Df_YandexMoney_Model_Action_CustomerReturn::_process()
	 * @return void
	 */
	public static function off() {self::session()->unsetData(self::$REDIRECTED);}

	/**
	 * @used-by Df_Payment_RedirectController::indexAction()
	 * @return void
	 */
	public static function on() {self::session()->setData(self::$REDIRECTED, true);}

	/** @return void */
	private function cancelOrderIfExists() {
		/** @var Df_Sales_Model_Order|null $order */
		$order = df_last_order(false);
		if ($order) {
			/**
			 * После вызова @see Mage_Sales_Model_Order::cancel()
			 * надо сохранить весь заказ целиком,
			 * поэтому вместо @see Df_Sales_Model_Order::addAndSaveStatusHistoryComment()
			 * используем низкоуровневую реализацию.
			 */
			$order->cancel();
			/** @var Mage_Sales_Model_Order_Status_History $history */
			$history = $order->addStatusHistoryComment(
				'Оплата заказа была прервана покупателем.', Mage_Sales_Model_Order::STATE_CANCELED
			);
			$history->setIsCustomerNotified(false);
			$order->save();
		}
	}

	/** @return void */
	private static function restoreQuoteIfExists() {
		/** @var int|null $quoteId */
		$quoteId = self::session()->getData('last_success_quote_id');
		if ($quoteId) {
			/** @var Df_Sales_Model_Quote $result */
			$quote = Df_Sales_Model_Quote::ld($quoteId);
			$quote->setIsActive(true);
			$quote->save();
		}
	}

	/** @return Mage_Checkout_Model_Session */
	private static function session() {return df_session_checkout();}

	/**
	 * Флаг @see $REDIRECTED предназначен для отслеживания возвращения покупателя
	 * с сайта платёжной системы без оплаты.
	 * Если этот флаг установлен — значит, покупатель был перенаправлен
	 * на сайт платёжной системы.
	 * @used-by is()
	 * @used-by off()
	 * @used-by on()
	 * @var string
	 */
	private static $REDIRECTED = 'rm__redirected_to_payment_system';
}