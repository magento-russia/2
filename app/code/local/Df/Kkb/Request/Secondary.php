<?php
/** @method Df_Kkb_Method method() */
abstract class Df_Kkb_Request_Secondary extends \Df\Payment\Request\Transaction {
	/** @return string */
	abstract public function getTransactionType();

	/**
	 * 2015-03-09
	 * Переопределяем метод с целью сделать его публичным конкретно для данного класса.
	 * @override
	 * @see \Df\Payment\Request\Transaction::amount()
	 * @used-by Df_Kkb_RequestDocument_Signed::amount()
	 * @see Df_Kkb_Request_Payment::amount()
	 * @return Df_Core_Model_Money
	 */
	public function amount() {return parent::amount();}

	/**
	 * Используется только для диагностики!
	 * @override
	 * @see \Df\Payment\Request\Secondary::_params()
	 * @used-by \Df\Payment\Request\Secondary::params()
	 * @return array(string => string|int)
	 */
	protected function _params() {return array('document' => $this->getRequestDocument()->getXml());}

	/**
	 * @override
	 * @return string
	 */
	public function getPaymentExternalId() {return $this->getResponsePayment()->getPaymentId();}
	
	/**
	 * @override
	 * @return Df_Kkb_Response_Secondary
	 */
	public function getResponse() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Kkb_Response_Secondary::i($this->getResponseAsXml());
			$this->{__METHOD__}->postProcess($this->payment());
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Kkb_Response_Payment */
	public function getResponsePayment() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Kkb_Response_Payment::i()->loadFromPaymentInfo($this->payment())
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return Zend_Uri_Http
	 */
	public function getUri() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Zend_Uri::factory(
				strtr('https://{host}/jsp/remote/control.jsp?{document}', array(
					'{host}' => $this->getHost()
					,'{document}' => rawurlencode($this->getRequestDocument()->getXml())
				)
			));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by Df_Kkb_RequestDocument_Signed::getOrderId()
	 * @see Df_Kkb_Request_Payment::orderId()
	 * @return string
	 */
	public function orderId() {return $this->order()->getIncrementId();}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getResponseAsArray() {df_abstract($this); return null;}

	/** @return string */
	private function getHost() {return
		$this->method()->isTestMode() ? '3dsecure.kkb.kz' : 'epay.kkb.kz'
	;}
	
	/** @return Df_Kkb_RequestDocument_Secondary */
	private function getRequestDocument() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Kkb_RequestDocument_Secondary::i($this);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getResponseAsXml() {
		if (!isset($this->{__METHOD__})) {
			/** @var Zend_Http_Client $httpClient */
			$httpClient = new Zend_Http_Client();
			$httpClient
				->setHeaders(array())
				->setUri($this->getUri())
				->setConfig(array('timeout' => 10))
				->setMethod(Zend_Http_Client::GET)
			;
			/** @var Zend_Http_Response $response */
			$this->{__METHOD__} = df_trim($httpClient->request()->getBody());
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}
}