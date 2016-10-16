<?php
/**
 * @method Df_YandexMoney_Model_Payment getMethod()
 * @method Df_YandexMoney_Model_Config_Area_Service configS()
 */
class Df_YandexMoney_Model_Action_CustomerReturn extends Df_Payment_Model_Action_Confirm {
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
	protected function getSignatureFromOwnCalculations() {df_should_not_be_here(); return null;}

	/**
	 * @override
	 * @param Exception $e
	 * @return void
	 */
	protected function processException(Exception $e) {
		/** @var bool $isPaymentException */
		$isPaymentException = ($e instanceof Df_Payment_Exception);
		if ($isPaymentException) {
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
			$this->processOrderCanNotInvoice();
		}
		else {
			while ($this->getResponseCapture()->needRetry()) {
				usleep(1000 * $this->getResponseCapture()->getDelayInMilliseconds());
				$this->resetRequestCapture();
			}
			/**
			 * Вызывать $this->getResponseCapture()->throwOnFailure()
			 * здесь не надо, потому что throwOnFailure() автоматически вызывается в методе
			 * @see Df_Payment_Model_Request_Secondary::getResponse()
			 * опосредованно через postProcess()
			 */
			/** @var Mage_Sales_Model_Order_Invoice $invoice */
			$invoice = $this->order()->prepareInvoice();
			$invoice->register();
			$invoice->capture();
			$this->saveInvoice($invoice);
			$this->order()->setState(
				Mage_Sales_Model_Order::STATE_PROCESSING
				,Mage_Sales_Model_Order::STATE_PROCESSING
				,df_sprintf($this->getMessage(self::CONFIG_KEY__MESSAGE__SUCCESS), $invoice->getIncrementId())
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
			 * потому что @see Df_YandexMoney_Model_Action_CustomerReturn::process()
			 * обрабатывает именно сессию покупателя, а не запрос платёжной системы
			 */
			Df_Payment_Redirected::off();
		}
		$this->redirectToSuccess();
	}

	/** @return Df_YandexMoney_Model_Request_Authorize */
	private function getRequestAuthorize() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_YandexMoney_Model_Request_Authorize::i(
				$this->payment(), $this->getToken()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_YandexMoney_Model_Request_Capture */
	private function getRequestCapture() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_YandexMoney_Model_Request_Capture::i(
				$this->payment(), $this->getResponseAuthorize(), $this->getToken()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_YandexMoney_Model_Response_Authorize */
	private function getResponseAuthorize() {return $this->getRequestAuthorize()->getResponse();}

	/** @return Df_YandexMoney_Model_Response_Capture */
	private function getResponseCapture() {return $this->getRequestCapture()->getResponse();}

	/** @return string */
	private function getToken() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_YandexMoney_Model_OAuth::i(
				$this->configS()->getAppId()
				, $this->configS()->getAppPassword()
				, $this->getTokenTemporary()
				, $this->getMethod()->getCustomerReturnUrl($this->order())
			)->getToken();
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getTokenTemporary() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $errorDescripion */
			$errorDescripion = $this->getRequest()->getParam('error_description');
			if ($errorDescripion) {
				$this->throwException($errorDescripion);
			}
			/** @var string|null $errorCode */
			$errorCode = $this->getRequest()->getParam('error');
			if ($errorCode) {
				$this->throwException(dfa(array(
					'invalid_request' =>
						'В запросе отсутствуют обязательные параметры,'
						. ' либо параметры имеют некорректные или недопустимые значения.'
					,'invalid_scope' =>
						'Параметр scope отсутствует,'
						. ' имеет некорректное значение или логические противоречия.'
					,'unauthorized_client' =>
						'Значение параметра «client_id» неверно,'
						. ' либо приложение не имеет право запрашивать авторизацию'
						. ' (например его client_id заблокирован Яндекс.Деньгами).'
					,'access_denied' => 'Пользователь отклонил запрос авторизации приложения.'
				), $errorCode, $errorCode));
			}
			$this->{__METHOD__} = $this->getRequest()->getParam('code');
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return void */
	private function resetRequestCapture() {unset($this->{__CLASS__ . '::getRequestCapture'});}
}


 