<?php
/** @method Df_Psbank_Model_Payment getMethod() */
class Df_Psbank_Model_Action_CustomerReturn extends Df_Payment_Model_Action_Confirm {
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
	protected function getSignatureFromOwnCalculations() {df_should_not_be_here(__METHOD__);}

	/**
	 * @override
	 * @param Exception $e
	 * @return void
	 */
	protected function processException(Exception $e) {
		Mage::logException($e);
		$this->addErrorMessageToSession($e);
		$this->redirectToCheckout();
	}

	/**
	 * @override
	 * @see Df_Payment_Model_Action_Confirm::_process()
	 * @used-by Df_Core_Model_Action::process()
	 * @return void
	 */
	protected function _process() {
		if ($this->getResponsePayment()->isSuccessful()) {
			$this->redirectToSuccess();
		}
		else {
			$this->addErrorMessageToSession();
			$this->redirectToFail();
		}
	}

	/**
	 * @param Exception|null $exception [optional]
	 */
	private function addErrorMessageToSession($exception = null) {
		try {
			/**
			 * Обратите внимание,
			 * что при возвращении на страницу RM_URL_CHECKOUT
			 * диагностическое сообщение надо добавлять в rm_session_core(),
			 * а не в rm_session_checkout(),
			 * потому что сообщения сессии checkout
			 * не отображаются в стандартной теме на странице checkout/onepage
			 */
			rm_session_core()->addError(strtr($this->getMethod()->configF()->getMessageFailure(), array(
				'{сообщение от платёжного шлюза}' => $this->getResponsePayment()->getStatusMeaning()
			)));
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
			 * что при возвращении на страницу RM_URL_CHECKOUT
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
					."<br/>Вероятно, администратор магазина"
					." указал банку неверный веб-адрес для получения таких оповещений."
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
		return Df_Psbank_Model_Response::i($transactionType)->loadFromPaymentInfo($this->info());
	}
}