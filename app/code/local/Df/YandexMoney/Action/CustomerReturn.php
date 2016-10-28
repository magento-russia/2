<?php
namespace Df\YandexMoney\Action;
/**
 * @method \Df\YandexMoney\Method method()
 * @method \Df\YandexMoney\Config\Area\Service configS()
 */
class CustomerReturn extends \Df\Payment\Action\Confirm {
	/**
	 * Использовать getConst нельзя из-за рекурсии.
	 * @override
	 * @return string
	 */
	protected function rkOII() {
		return \Df\Payment\Method\WithRedirect::REQUEST_PARAM__ORDER_INCREMENT_ID;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function signatureOwn() {df_should_not_be_here(); return null;}

	/**
	 * @override
	 * @param \Exception $e
	 * @return void
	 */
	protected function processException(\Exception $e) {
		/** @var bool $isPaymentException */
		$isPaymentException = ($e instanceof \Df\Payment\Exception);
		if ($isPaymentException) {
			$this->logException($e);
		}
		else {
			\Mage::logException($e);
		}
		$this->showExceptionOnCheckoutScreen($e);
		$this->redirectToCheckout();
	}

	/**
	 * @override
	 * @see \Df\Payment\Action\Confirm::_process()
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
			 * @see \Df\Payment\Request\Secondary::getResponse()
			 * опосредованно через postProcess()
			 */
			/** @var \Mage_Sales_Model_Order_Invoice $invoice */
			$invoice = $this->order()->prepareInvoice();
			$invoice->register();
			$invoice->capture();
			$this->saveInvoice($invoice);
			$this->order()->setState(
				\Mage_Sales_Model_Order::STATE_PROCESSING
				,\Mage_Sales_Model_Order::STATE_PROCESSING
				,df_sprintf($this->messageSuccess($invoice), $invoice->getIncrementId())
				,true
			);
			$this->order()->save();
			$this->order()->sendNewOrderEmail();
			$this->redirectToSuccess();
			/**
			 * В отличие от метода
			 * @see \Df\Payment\Action\Confirm::process()
			 * здесь необходимость вызова
			 * @uses \Df\Payment\Redirected::off() не вызывает сомнений,
			 * потому что @see \Df\YandexMoney\Action\CustomerReturn::process()
			 * обрабатывает именно сессию покупателя, а не запрос платёжной системы
			 */
			\Df\Payment\Redirected::off();
		}
		$this->redirectToSuccess();
	}

	/** @return \Df\YandexMoney\Request\Authorize */
	private function getRequestAuthorize() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = \Df\YandexMoney\Request\Authorize::i(
				$this->payment(), $this->getToken()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return \Df\YandexMoney\Request\Capture */
	private function getRequestCapture() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = \Df\YandexMoney\Request\Capture::i(
				$this->payment(), $this->getResponseAuthorize(), $this->getToken()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return \Df\YandexMoney\Response\Authorize */
	private function getResponseAuthorize() {return $this->getRequestAuthorize()->getResponse();}

	/** @return \Df\YandexMoney\Response\Capture */
	private function getResponseCapture() {return $this->getRequestCapture()->getResponse();}

	/** @return string */
	private function getToken() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = \Df\YandexMoney\OAuth::i(
				$this->configS()->getAppId()
				, $this->configS()->getAppPassword()
				, $this->getTokenTemporary()
				, $this->method()->getCustomerReturnUrl($this->order())
			)->getToken();
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getTokenTemporary() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $errorDescripion */
			$errorDescripion = $this->param('error_description');
			if ($errorDescripion) {
				$this->throwException($errorDescripion);
			}
			/** @var string|null $errorCode */
			$errorCode = $this->param('error');
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
			$this->{__METHOD__} = $this->param('code');
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return void */
	private function resetRequestCapture() {unset($this->{__CLASS__ . '::getRequestCapture'});}
}


 