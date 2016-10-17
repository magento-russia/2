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
		catch (Exception $e) {
			df_handle_entry_point_exception($e, false);
		}
		$this->getResponse()->setRedirect(
			isset($this->_redirectUrl) ? $this->_redirectUrl : df_url_checkout_success()
		);
	}

	/** @return \Df\Xml\X */
	private function e() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_xml_parse(base64_decode($this->getRequest()->getParam('operation_xml')));
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getProcessorMethodName() {
		/** @var string $status */
		$status = df_leaf_child($this->e(), 'status');
		if (Df_LiqPay_Action_Confirm::PAYMENT_STATE__WAIT_SECURE === $status) {
			$status = Df_LiqPay_Action_Confirm::PAYMENT_STATE__SUCCESS;
		}
		/** @var string $result */
		$result = 'process' . ucfirst($status);
		/**
		 * Обратите внимание, что проверять наличие метода
		 * надо посредством @see method_exists(), а не @see is_callable(), по двум причинам:
		 * 1) проверяемый метод — приватный
		 * (@see is_callable() для приватных методов не работает и всегда возвращает false)
		 * 2) Наличие @see Varien_Object::__call()
		 * приводит к тому, что @see is_callable() всегда возвращает true
		 */
		return method_exists($this, $result) ? $result : 'processSuccess';
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @used-by processPaymentStatusCode()
	 * http://php.net/manual/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * http://3v4l.org/OipEQ
	 * @return void
	 */
	private function processDelayed() {
		$this->setRedirectUrl(df_url_checkout_success());
		df_last_order()->comment(
			'Покупатель решил оплатить заказ через терминал Приватбанка. Ждём оплату.'
		);
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @used-by processPaymentStatusCode()
	 * http://php.net/manual/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * http://3v4l.org/OipEQ
	 * @return void
	 */
	private function processFailure() {$this->setRedirectUrl(df_url_checkout_fail());}

	/**
	 * @uses processDelayed()
	 * @uses processFailure()
	 * @uses processSuccess()
	 * @return void
	 */
	private function processPaymentStatusCode() {
		call_user_func(array($this, $this->getProcessorMethodName()));
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @used-by processPaymentStatusCode()
	 * http://php.net/manual/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * http://3v4l.org/OipEQ
	 * @return void
	 */
	private function processSuccess() {$this->setRedirectUrl(df_url_checkout_success());}

	/**
	 * @param string $redirectUrl
	 * @return void
	 */
	private function setRedirectUrl($redirectUrl) {$this->_redirectUrl = $redirectUrl;}

	/** @var string */
	private $_redirectUrl;
}