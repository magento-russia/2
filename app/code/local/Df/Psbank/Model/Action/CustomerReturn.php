<?php
/**
 * @method Df_Psbank_Model_Payment getPaymentMethod()
 */
class Df_Psbank_Model_Action_CustomerReturn extends Df_Payment_Model_Action_Confirm {
	/**
	 * @override
	 * @return Df_Psbank_Model_Action_CustomerReturn
	 */
	public function process() {
		try {
			$this->getResponse()->setRedirect(
				$this->getResponsePayment()->isSuccessful()
				? df_h()->payment()->url()->getCheckoutSuccess()
				: df_h()->payment()->url()->getCheckoutFail()
			);
			if (!$this->getResponsePayment()->isSuccessful()) {
				$this->addErrorMessageToSession();
			}
		}
		catch (Exception $e) {
			Mage::logException($e);
			$this->addErrorMessageToSession($e);
			$this->getResponse()->setRedirect(df_h()->payment()->url()->getCheckout());
		}
		return $this;
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
	protected function getSignatureFromOwnCalculations() {
		df_should_not_be_here(__METHOD__);
		return '';
	}

	/**
	 * @param Exception|null $exception [optional]
	 */
	private function addErrorMessageToSession($exception = null) {
		try {
			/**
			 * Обратите внимание,
			 * что при возвращении на страницу Df_Checkout_Const::URL__CHECKOUT
			 * диагностическое сообщение надо добавлять в rm_session_core(),
			 * а не в rm_session_checkout(),
			 * потому что сообщения сессии checkout
			 * не отображаются в стандартной теме на странице checkout/onepage
			 */
			rm_session_core()->addError(strtr(
				$this->getPaymentMethod()->getRmConfig()->frontend()->getMessageFailure()
				,array('{сообщение от платёжного шлюза}' => $this->getResponsePayment()->getStatusMeaning())
			));
		}
		catch (Exception $newException) {
			if ($exception) {
				df_notify_exception($newException);
			}
			if (!$exception || ($newException->getMessage() !== $exception->getMessage())) {
				df_notify_exception($newException);
			}
			/**
			 * Обратите внимание,
			 * что при возвращении на страницу Df_Checkout_Const::URL__CHECKOUT
			 * диагностическое сообщение надо добавлять в rm_session_core(),
			 * а не в rm_session_checkout(),
			 * потому что сообщения сессии checkout
			 * не отображаются в стандартной теме на странице checkout/onepage
			 */
			rm_session_core()->addError(rm_ets($exception ? $exception : $newException));
		}

	}
	
	/** @return Df_Psbank_Model_Response */
	private function getResponsePayment() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Psbank_Model_Response $result */
			$result =
				$this->getResponseByTransactionType(
					Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH
				)
			;
			if (!df_check_string_not_empty($result->getData('RESULT'))) {
				$result =
					$this->getResponseByTransactionType(
						Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT
					)
				;
			}
			if (!df_check_string_not_empty($result->getData('RESULT'))) {
				df_error(
					'Интернет-магазин не получал от банка подтверждения платежа.'
					."<br/>Вероятно, администратор магазина указал банку неверный веб-адрес для получения таких оповещений."
				);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $transactionType
	 * @return Df_Psbank_Model_Response
	 */
	private function getResponseByTransactionType($transactionType) {
		return Df_Psbank_Model_Response::i($transactionType)
			->loadFromPaymentInfo($this->getPaymentMethod()->getInfoInstance())
		;
	}

	/**
	 * @static
	 * @param Df_Psbank_CustomerReturnController $controller
	 * @return Df_Psbank_Model_Action_CustomerReturn
	 */
	public static function i(Df_Psbank_CustomerReturnController $controller) {
		return new self(array(self::P__CONTROLLER => $controller));
	}
}