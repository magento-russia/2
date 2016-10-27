<?php
namespace Df\Kkb\Request;
/** @method \Df\Kkb\Method method() */
abstract class Secondary extends \Df\Payment\Request\Transaction {
	/** @return string */
	abstract public function getTransactionType();

	/**
	 * 2015-03-09
	 * Переопределяем метод с целью сделать его публичным конкретно для данного класса.
	 * @override
	 * @see \Df\Payment\Request\Transaction::amount()
	 * @used-by \Df\Kkb\RequestDocument\Signed::amount()
	 * @see \Df\Kkb\Request\Payment::amount()
	 * @return \Df_Core_Model_Money
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
	 * @return \Df\Kkb\Response\Secondary
	 */
	public function getResponse() {return dfc($this, function() {
		/** @var \Df\Kkb\Response\Secondary $result */
		$result = \Df\Kkb\Response\Secondary::i($this->getResponseAsXml());
		$result->postProcess($this->payment());
		return $result;
	});}

	/** @return \Df\Kkb\Response\Payment */
	public function getResponsePayment() {return dfc($this, function() {return
		\Df\Kkb\Response\Payment::i()->loadFromPaymentInfo($this->payment())
	;});}

	/**
	 * @override
	 * @return \Zend_Uri_Http
	 */
	public function getUri() {return dfc($this, function() {return
		\Zend_Uri::factory(
			"https://{$this->getHost()}/jsp/remote/control.jsp?"
			. rawurlencode($this->getRequestDocument()->getXml())
		)
	;});}

	/**
	 * @used-by \Df\Kkb\RequestDocument\Signed::getOrderId()
	 * @see \Df\Kkb\Request\Payment::orderId()
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
	
	/** @return \Df\Kkb\RequestDocument\Secondary */
	private function getRequestDocument() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = \Df\Kkb\RequestDocument\Secondary::i($this);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getResponseAsXml() {return dfc($this, function() {
		/** @var \Zend_Http_Client $httpClient */
		$httpClient = new \Zend_Http_Client();
		$httpClient
			->setHeaders(array())
			->setUri($this->getUri())
			->setConfig(array('timeout' => 10))
			->setMethod(\Zend_Http_Client::GET)
		;
		/** @var string $result */
		$result = df_trim($httpClient->request()->getBody());
		df_result_string_not_empty($result);
		return $result;
	});}
}