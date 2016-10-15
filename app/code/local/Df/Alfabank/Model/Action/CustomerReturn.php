<?php
/** @method Df_Alfabank_Model_Payment getMethod() */
class Df_Alfabank_Model_Action_CustomerReturn extends Df_Payment_Model_Action_Confirm {
	/**
	 * @override
	 * @see Df_Core_Model_Action::getRequest()
	 * @return Zend_Controller_Request_Abstract
	 */
	protected function getRequest() {
		if (!isset($this->{__METHOD__})) {
			/** @var Zend_Controller_Request_Abstract $result */
			$this->{__METHOD__} = new Zend_Controller_Request_Http();
			$this->{__METHOD__}->setParams(
				$this->getRequestState()->getResponse()->getData() + parent::getRequest()->getParams()
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * Использовать getConst нельзя из-за рекурсии.
	 * @override
	 * @return string
	 */
	protected function getRequestKeyOrderIncrementId() {
		return Df_Payment_Model_Method_WithRedirect::REQUEST_PARAM__ORDER_INCREMENT_ID;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getRequestValuePaymentAmountAsString() {
		return strval(df_float(parent::getRequestValuePaymentAmountAsString()) / 100);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getSignatureFromOwnCalculations() {df_should_not_be_here(__METHOD__);}

	/**
	 * @override
	 * @param Exception $e
	 * @return void
	 */
	protected function processException(Exception $e) {
		if ($e instanceof Df_Payment_Exception && isset($this->{__CLASS__ . '::getRequestState'})) {
			$this->logException($e);
		}
		else {
			Mage::logException($e);
		}
		$this->showExceptionOnCheckoutScreen($e);
		$this->redirectToCheckout();
	}

	/**
	 * @override
	 * @see Df_Payment_Model_Action_Confirm::_process()
	 * @used-by Df_Core_Model_Action::process()
	 * @return void
	 */
	protected function _process() {
		if (!$this->order()->canInvoice()) {
			/**
			 * Бывают платёжные системы (например, «Единая касса»),
			 * которые, согласно их документации,
			 * могут несколько раз присылать подтверждение оплаты покупателем
			 * одного и того же заказа.
			 *
			 * Так вот, данная проверка гарантирует, что платёжный модуль не будет пытаться
			 * принять повторно оплату за уже оплаченный заказ.
			 *
			 * Обратите внимание, что проверку заказа на оплаченность
			 * надо сделать до вызова метода checkPaymentAmount,
			 * потому что иначе требуемая к оплате сумма будет равна нулю,
			 * и checkPaymentAmount будет сравнивать сумму от платёжной системы с нулём.
			 */
			$this->processOrderCanNotInvoice();
		}
		else {
			$this->checkPaymentAmount();
			if (810 !== df_nat($this->getRequestValuePaymentCurrencyCode())) {
				df_error('Заказ был оплачен не в рублях');
			}
			/** @var Mage_Sales_Model_Order_Invoice $invoice */
			$invoice = $this->order()->prepareInvoice();
			$invoice->register();
			/**
			 * Код «2» означает, что средства с карты покупателя были списаны.
			 * Код «1» означает, что средства с карты покупателя были зарезервированы.
			 * Обратите внимание, что при резервировании средств мы не вызываем $invoice->capture()
			 * (что переводило бы счёт в состояние «оплачен»),
			 * а вместо этого оставляем счёт в открытом состоянии,
			 * что даёт администратору возможность снять зарезервированные покупателем средства
			 * посредством администтративного интерфейса интернет-магазина:
			 * на странице счёта появляется кнопка «Принять оплату» («Capture»).
			 */
			if ($this->getResponseState()->isTransactionClosed()) {
				$invoice->capture();
			}
			$this->saveInvoice($invoice);
			$this->order()->setState(
				Mage_Sales_Model_Order::STATE_PROCESSING
				,Mage_Sales_Model_Order::STATE_PROCESSING
				,df_sprintf(
					$this->getMessage(self::CONFIG_KEY__MESSAGE__SUCCESS)
					,$invoice->getIncrementId()
				)
				,true
			);
			$this->order()->save();
			$this->order()->sendNewOrderEmail();
			$this->redirectToSuccess();
			/**
			 * В отличие от метода
			 * @see Df_Payment_Model_Action_Confirm::process()
			 * здесь необходимость вызова
			 * @uses Df_Payment_Redirected::off() не вызывает сомнений,
			 * потому что @see Df_Alfabank_Model_Action_CustomerReturn:process()
			 * обрабатывает именно сессию покупателя, а не запрос платёжной системы
			 */
			Df_Payment_Redirected::off();
		}
	}

	/**
	 * @override
	 * @param Exception $e
	 * @return void
	 */
	protected function processResponseForError(Exception $e) {$this->redirectToFail();}
	
	/** @return Df_Alfabank_Model_Request_State */
	private function getRequestState() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Alfabank_Model_Request_State::i($this->payment());
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Alfabank_Model_Response_State */
	private function getResponseState() {return $this->getRequestState()->getResponse();}
}