<?php
class Df_IPay_CustomerReturnController extends Mage_Core_Controller_Front_Action {
	/**
	 * Платёжная система присылает сюда подтверждение приёма оплаты от покупателя.
	 * @return void
	 */
	public function indexAction() {
		try {
			$this->setRedirectUrl(
				(Mage_Sales_Model_Order::STATE_PROCESSING === $this->getOrder()->getState())
				? df_h()->payment()->url()->getCheckoutSuccess()
				: df_h()->payment()->url()->getCheckoutFail()
			);
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e, false);
		}
		$this->getResponse()->setRedirect($this->getRedirectUrl());
	}

	/** @return Df_IPay_CustomerReturnController */
	public function processDelayed() {
		$this->setRedirectUrl(df_h()->payment()->url()->getCheckoutSuccess());
		$this->getOrder()->addStatusHistoryComment(
			'Покупатель решил оплатить заказ через терминал Приватбанка. Ждём оплату.'
		);
		$this->getOrder()->setData(Df_Sales_Const::ORDER_PARAM__IS_CUSTOMER_NOTIFIED, false);
		$this->getOrder()->save();
		return $this;
	}

	/** @return Df_IPay_CustomerReturnController */
	public function processFailure() {
		$this->setRedirectUrl(df_h()->payment()->url()->getCheckoutFail());
		return $this;
	}

	/** @return Df_IPay_CustomerReturnController */
	public function processSuccess() {
		$this->setRedirectUrl(df_h()->payment()->url()->getCheckoutSuccess());
		return $this;
	}

	/** @return Df_Sales_Model_Order */
	private function getOrder() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Sales_Model_Order::i();
			$this->{__METHOD__}->loadByIncrementId(
				rm_session_checkout()->getData(Df_Checkout_Const::SESSION_PARAM__LAST_REAL_ORDER_ID)
			);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getRedirectUrl() {
		// Обратите внимание, что свойство _redirectUrl
		// может быть ранее установлено методом setRedirectUrl
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_h()->payment()->url()->getCheckoutSuccess();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $redirectUrl
	 * @return string
	 */
	private function setRedirectUrl($redirectUrl) {
		df_param_string($redirectUrl, 0);
		$this->{__CLASS__ . '::getRedirectUrl'} = $redirectUrl;
		return $this;
	}
}