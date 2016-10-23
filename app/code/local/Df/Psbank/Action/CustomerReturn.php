<?php
/** @method Df_Psbank_Method method() */
class Df_Psbank_Action_CustomerReturn extends Df_Payment_Action_Confirm {
	/**
	 * Использовать getConst нельзя из-за рекурсии.
	 * @override
	 * @return string
	 */
	protected function rkOII() {
		return Df_Payment_Method_WithRedirect::REQUEST_PARAM__ORDER_INCREMENT_ID;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function signatureOwn() {df_should_not_be_here(); return null;}

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
	 * @see Df_Payment_Action_Confirm::_process()
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
			 * диагностическое сообщение надо добавлять в df_session_core(),
			 * а не в df_session_checkout(),
			 * потому что сообщения сессии checkout
			 * не отображаются в стандартной теме на странице checkout/onepage
			 */
			df_session_core()->addError(strtr($this->method()->configF()->getMessageFailure(), array(
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
			 * диагностическое сообщение надо добавлять в df_session_core(),
			 * а не в df_session_checkout(),
			 * потому что сообщения сессии checkout
			 * не отображаются в стандартной теме на странице checkout/onepage
			 */
			df_session_core()->addError(df_ets($exception ? $exception : $newException));
		}

	}
	
	/** @return Df_Psbank_Response */
	private function getResponsePayment() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Psbank_Response $result */
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
	 * @return Df_Psbank_Response
	 */
	private function getResponseByTransactionType($transactionType) {return
		Df_Psbank_Response::i($transactionType)->loadFromPaymentInfo($this->ii())
	;}
}