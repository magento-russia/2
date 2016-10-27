<?php
namespace Df\Payment;
use Df_Sales_Model_Order as O;
use Df_Sales_Model_Quote as Q;
class Redirected extends \Df_Core_Model {
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
	 * Флаг \Df\Payment\Redirected::SESSION_PARAM__REDIRECTED_TO_PAYMENT_SYSTEM
	 * предназначен для отслеживания возвращения покупателя
	 * с сайта платёжной системы без оплаты.
	 * Если этот флаг установлен — значит, покупатель был перенаправлен
	 * на сайт платёжной системы.
	 * @used-by Df_Checkout_Observer::controller_action_predispatch_checkout()
	 * @used-by Df_Payment_RedirectController::indexAction()
	 * @return bool
	 */
	public static function is() {return df_bool(self::session()->getData(self::$REDIRECTED));}

	/**
	 * @used-by \Df\Alfabank\Action\CustomerReturn::_process()
	 * @used-by \Df\Avangard\Action\CustomerReturn::_process()
	 * @used-by Df_Checkout_Observer::controller_action_predispatch_checkout()
	 * @used-by \Df\Payment\Action\Confirm::_process()
	 * @used-by Df_YandexMoney_Action_CustomerReturn::_process()
	 * @return void
	 */
	public static function off() {self::session()->unsetData(self::$REDIRECTED);}

	/**
	 * @used-by Df_Payment_RedirectController::indexAction()
	 * @return void
	 */
	public static function on() {self::session()->setData(self::$REDIRECTED, true);}

	/** @return void */
	private static function cancelOrderIfExists() {
		/** @var O|null $order */
		$order = df_last_order(false);
		if ($order) {
			/**
			 * После вызова @see Mage_Sales_Model_Order::cancel()
			 * надо сохранить весь заказ целиком,
			 * поэтому вместо @see Df_Sales_Model_Order::addAndSaveStatusHistoryComment()
			 * используем низкоуровневую реализацию.
			 */
			$order->cancel();
			/** @var \Mage_Sales_Model_Order_Status_History $history */
			$history = $order->addStatusHistoryComment(
				'Оплата заказа была прервана покупателем.', O::STATE_CANCELED
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
			/** @var Q $result */
			$quote = Q::ld($quoteId);
			$quote->setIsActive(true);
			$quote->save();
		}
	}

	/** @return \Mage_Checkout_Model_Session */
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
	private static $REDIRECTED = 'df__redirected_to_payment_system';
}