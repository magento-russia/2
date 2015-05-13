<?php
class Df_LiqPay_CustomerReturnController extends Mage_Core_Controller_Front_Action {
	/**
	 * Платёжная система присылает сюда подтверждение приёма оплаты от покупателя.
	 * @return void
	 */
	public function indexAction() {
		try {
			$this->processPaymentStatusCode();
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e, false);
		}
		$this->getResponse()->setRedirect($this->getRedirectUrl());
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

	/** @return array(string => string) */
	private function getPaymentInfoAsArray() {
		return $this->getPaymentInfoAsVarienXml()->asCanonicalArray();
	}

	/** @return Df_Varien_Simplexml_Element */
	private function getPaymentInfoAsVarienXml() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_xml(base64_decode($this->getRequest()->getParam('operation_xml')));
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getPaymentStatusCode() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$this->{__METHOD__} = df_a($this->getPaymentInfoAsArray(), 'status');
			df_result_string($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getProcessorMethodName() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result =
				df_concat(
					'process'
					,uc_words(
						$this->translatePaymentStatusCodeToProcessorCode(
							$this->getPaymentStatusCode()
						)
						,// $destSep
						''
					)
				)
			;
			if (
				/**
				 * Обратите внимание, что проверять наличие метода
				 * надо посредством method_exists, а не is_callable, по двум причинам:
				 * 1) проверяемый метод — приватный
				 * (is_callable для приватных методов не работает и всегда возвращает false)
				 * 2) Наличие Varien_Object::__call
				 * приводит к тому, что is_callable всегда возвращает true
				 */
				!method_exists($this, $result)
			) {
				$result = 'processSuccess';
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getRedirectUrl() {
		/**
		 * Обратите внимание, что свойство $this->{__METHOD__}
		 * могло быть ранее инициализировано методом @see setRedirectUrl()
		 */
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_h()->payment()->url()->getCheckoutSuccess();
		}
		return $this->{__METHOD__};
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @link http://php.net/manual/en/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * @link http://3v4l.org/OipEQ
	 * @return void
	 */
	private function processDelayed() {
		$this->setRedirectUrl(df_h()->payment()->url()->getCheckoutSuccess());
		$this->getOrder()
			->addStatusHistoryComment(
				'Покупатель решил оплатить заказ через терминал Приватбанка. Ждём оплату.'
			)
		;
		$this->getOrder()->setData(Df_Sales_Const::ORDER_PARAM__IS_CUSTOMER_NOTIFIED, false);
		$this->getOrder()->save();
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @link http://php.net/manual/en/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * @link http://3v4l.org/OipEQ
	 * @return void
	 */
	private function processFailure() {
		$this->setRedirectUrl(df_h()->payment()->url()->getCheckoutFail());
	}

	/** @return void */
	private function processPaymentStatusCode() {
		call_user_func(array($this, $this->getProcessorMethodName()));
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @link http://php.net/manual/en/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * @link http://3v4l.org/OipEQ
	 * @return void
	 */
	private function processSuccess() {
		$this->setRedirectUrl(df_h()->payment()->url()->getCheckoutSuccess());
	}

	/**
	 * @param string  $redirectUrl
	 * @return string
	 */
	private function setRedirectUrl($redirectUrl) {
		df_param_string($redirectUrl, 0);
		$this->{__CLASS__ . '::getRedirectUrl'} = $redirectUrl;
		return $this;
	}

	/**
	 * @param string $paymentStatusCode
	 * @return string
	 */
	private function translatePaymentStatusCodeToProcessorCode($paymentStatusCode) {
		df_param_string($paymentStatusCode, 0);
		return
			df_a(
				array(
						Df_LiqPay_Model_Action_Confirm::PAYMENT_STATE__WAIT_SECURE
					=>
						Df_LiqPay_Model_Action_Confirm::PAYMENT_STATE__SUCCESS
				)
				,$paymentStatusCode
				,$paymentStatusCode
			)
		;
	}
}