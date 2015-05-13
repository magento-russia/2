<?php
/**
 * @method Df_Alfabank_Model_Payment getPaymentMethod()
 */
class Df_Alfabank_Model_Action_CustomerReturn extends Df_Payment_Model_Action_Confirm {
	/**
	 * @override
	 * @return Df_Alfabank_Model_Action_CustomerReturn
	 */
	public function process() {
		try {
			if (!$this->getOrder()->canInvoice()) {
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
				if (810 !== rm_nat($this->getRequestValuePaymentCurrencyCode())) {
					df_error('Заказ был оплачен не в рублях');
				}
				/** @var Mage_Sales_Model_Order_Invoice $invoice */
				$invoice = $this->getOrder()->prepareInvoice();
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
				/** @var Mage_Core_Model_Resource_Transaction $coreTransaction */
				$coreTransaction = df_model(Df_Core_Const::CORE_RESOURCE_TRANSACTION_CLASS_MF);
				$coreTransaction
					->addObject($invoice)
					->addObject($invoice->getOrder())
					->save()
				;
				$this->getOrder()
					->setState(
						Mage_Sales_Model_Order::STATE_PROCESSING
						,Mage_Sales_Model_Order::STATE_PROCESSING
						,rm_sprintf(
							$this->getMessage(self::CONFIG_KEY__MESSAGE__SUCCESS)
							,$invoice->getIncrementId()
						)
						,true
					)
				;
				$this->getOrder()->save();
				$this->getOrder()->sendNewOrderEmail();
				$this->getResponse()->setRedirect(df_h()->payment()->url()->getCheckoutSuccess());
				/**
				 * В отличие от метода
				 * @see Df_Payment_Model_Action_Confirm::process()
				 * здесь необходимость вызова unsetRedirected() не вызывает сомнений,
				 * потому что Df_Alfabank_Model_Action_CustomerReturn:process()
				 * обрабатывает именно сессию покупателя, а не запрос платёжной системы
				 */
				Df_Payment_Model_Redirector::s()->unsetRedirected();
			}
		}
		catch (Exception $e) {
			/** @var bool $isPaymentException */
			$isPaymentException = ($e instanceof Df_Payment_Exception_Client);
			if ($isPaymentException && isset($this->{__CLASS__ . '::getRequestState'})) {
				$this->logException($e);
			}
			else {
				Mage::logException($e);
			}
			$this->showExceptionOnCheckoutScreen($e);
			$this->redirectToCheckoutScreen();
		}
		return $this;
	}

	/**
	 * @override
	 * @return Zend_Controller_Request_Abstract
	 */
	protected function getRequest() {
		if (!isset($this->{__METHOD__})) {
			/** @var Zend_Controller_Request_Abstract $result */
			$this->{__METHOD__} = new Zend_Controller_Request_Http();
			$this->{__METHOD__}->setParams(array_merge(
				parent::getRequest()->getParams()
				,$this->getRequestState()->getResponse()->getData()
			));
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
		return strval(rm_float(parent::getRequestValuePaymentAmountAsString()) / 100);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getSignatureFromOwnCalculations() {
		df_should_not_be_here(__METHOD__);
		return '';
	}

	/**
	 * @override
	 * @param Exception $e
	 * @return Df_Alfabank_Model_Action_CustomerReturn
	 */
	protected function processResponseForError(Exception $e) {
		$this->getResponse()->setRedirect(df_h()->payment()->url()->getCheckoutFail());
		return $this;
	}
	
	/** @return Df_Alfabank_Model_Request_State */
	private function getRequestState() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Alfabank_Model_Request_State::i(array(
					Df_Alfabank_Model_Request_State::P__ORDER_PAYMENT => $this->getOrder()->getPayment()
					,Df_Alfabank_Model_Request_State::P__PAYMENT_METHOD => $this->getPaymentMethod()
				))
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Alfabank_Model_Response_State */
	private function getResponseState() {return $this->getRequestState()->getResponse();}
	
	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param Df_Alfabank_CustomerReturnController $controller
	 * @return Df_Alfabank_Model_Action_CustomerReturn
	 */
	public static function i(Df_Alfabank_CustomerReturnController $controller) {
		return new self(array(self::P__CONTROLLER => $controller));
	}
}