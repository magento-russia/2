<?php
class Df_Payment_Model_Redirector extends Df_Core_Model {
	/** @return Df_Payment_Model_Redirector */
	public function restoreQuote() {
		if ($this->isOrderExists()) {
			$this->cancelOrder();
		}
		if ($this->isQuoteExists()) {
			$this->restoreQuoteInternal();
		}
		$this->unsetRedirected();
		return $this;
	}

	/** @return bool */
	public function isRedirected() {
		return
			rm_bool(
				/**
				 * Флаг Df_Payment_Model_Redirector::SESSION_PARAM__REDIRECTED_TO_PAYMENT_SYSTEM
				 * предназначен для отслеживания возвращения покупателя
				 * с сайта платёжной системы без оплаты.
				 * Если этот флаг установлен — значит, покупатель был перенаправлен
				 * на сайт платёжной системы.
				 */
				$this->getSession()->getData(self::SESSION_PARAM__REDIRECTED_TO_PAYMENT_SYSTEM)
			)
		;
	}

	/** @return Df_Payment_Model_Redirector */
	public function setRedirected() {
		$this->getSession()->setData(self::SESSION_PARAM__REDIRECTED_TO_PAYMENT_SYSTEM, true);
		return $this;
	}

	/** @return Df_Payment_Model_Redirector */
	public function unsetRedirected() {
		$this->getSession()->unsetData(self::SESSION_PARAM__REDIRECTED_TO_PAYMENT_SYSTEM);
		return $this;
	}

	/** @return Df_Payment_Model_Redirector */
	private function cancelOrder() {
		$this->getOrder()->cancel();
		$this->getOrder()->addStatusHistoryComment(
			self::MESSAGE__ADMIN, Mage_Sales_Model_Order::STATE_CANCELED
		);
		$this->getOrder()->setData('is_customer_notified', false);
		$this->getOrder()->save();
		return $this;
	}

	/**
	 * @private
	 * @return Df_Sales_Model_Order
	 */
	private function getOrder() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Sales_Model_Order::i();
			$this->{__METHOD__}->loadByIncrementId($this->getOrderIncrementId());
			df_assert($this->{__METHOD__}->getId());
		}
		return $this->{__METHOD__};
	}

	/** @return string|null */
	private function getOrderIncrementId() {
		return $this->getSession()->getData(Df_Checkout_Const::SESSION_PARAM__LAST_REAL_ORDER_ID);
	}

	/** @return Mage_Sales_Model_Quote */
	private function getQuote() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Sales_Model_Quote $result */
			$result = df_model(Df_Sales_Const::QUOTE_CLASS_MF);
			$result->load($this->getQuoteId());
			df_assert(!is_null($result->getId()));
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string|null */
	private function getQuoteId() {
		return $this->getSession()->getData(Df_Checkout_Const::SESSION_PARAM__LAST_SUCCESS_QUOTE_ID);
	}

	/** @return Mage_Checkout_Model_Session */
	private function getSession() {return rm_session_checkout();}

	/** @return bool */
	private function isOrderExists() {return !!$this->getOrderIncrementId();}

	/** @return bool */
	private function isQuoteExists() {return !!$this->getQuoteId();}

	/** @return Df_Payment_Model_Redirector */
	private function restoreQuoteInternal() {
		$this->getQuote()
			->setIsActive(true)
			->save()
		;
		return $this;
	}
	const MESSAGE__ADMIN = 'Оплата заказа была прервана покупателем.';
	const SESSION_PARAM__REDIRECTED_TO_PAYMENT_SYSTEM = 'rm__redirected_to_payment_system';
	/** @return Df_Payment_Model_Redirector */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}