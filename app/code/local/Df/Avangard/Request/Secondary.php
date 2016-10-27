<?php
namespace Df\Avangard\Request;
use Df\Avangard\RequestDocument as Document;
use Df\Avangard\Response\Registration as RegResponse;
/** @method \Df\Avangard\Method method() */
abstract class Secondary extends \Df\Payment\Request\Transaction {
	/** @return string */
	abstract protected function getRequestId();

	/**
	 * @override
	 * @see \Df\Payment\Request\Transaction::getUri()
	 * @return \Zend_Uri_Http
	 */
	public function getUri() {return dfc($this, function() {
		/** @var \Zend_Uri_Http $result */
		$result = \Zend_Uri::factory('https');
		$result->setHost('www.avangard.ru');
		$result->setPath("/iacq/h2h/{$this->getRequestId()}");
		return $result;
	});}

	/**
	 * @override
	 * @see \Df\Payment\Request\Secondary::_params()
	 * @used-by \Df\Payment\Request\Secondary::params()
	 * @return array(string => string|int)
	 */
	protected function _params() {return [
		'shop_id' => $this->shopId()
		,'shop_passwd' => $this->password()
		,'ticket' => $this->getPaymentExternalId()
	];}

	/**
	 * @override
	 * @return string
	 */
	protected function getPaymentExternalId() {return $this->regResponse()->getPaymentExternalId();}

	/**
	 * @override
	 * @see \Df\Payment\Request\Transaction::getResponseAsArray()
	 * @return array(string => string)
	 */
	protected function getResponseAsArray() {return dfc($this, function() {
		/** @var string $xml */
		$xml = $this->getHttpClient()->request()->getBody();
		df_report($this->getRequestId() . '-response-{date}-{time}.xml', $xml);
		return df_xml_parse($xml)->asCanonicalArray();
	});}

	/** @return \Zend_Http_Client */
	private function getHttpClient() {return dfc($this, function() {
		/** @var string $requestXml */
		$requestXml = Document::i($this->params(), $this->getRequestId())->getXml();
		df_report("{$this->getRequestId()}-request-{date}-{time}.xml", $requestXml);
		/** @var \Zend_Http_Client $result */
		$result = new \Zend_Http_Client();
		$result
			/**
			 * Чтобы внутренняя информационная система банка Авангард
			 * обработала наш запрос в правильной кодировке,
			 * пробуем явно указать кодировку содержимого
			 * заданием значения «%application/x-www-form-urlencoded; charset=utf-8»
			 * для заголовка HTTP «Content-Type» вместо автоматического значения
			 * «%application/x-www-form-urlencoded».
			 * Обратите внимание, что это нельзя сделать посредством
			 * Zend_Http_Client::setHeaders или Zend_Http_Client::setEncType,
			 * потому что иначе Zend_Http_Client возбудит исключительную ситуацию
			 * «Cannot handle content type
			 * 'application/x-www-form-urlencoded; charset=utf-8' automatically.
			 * Please use Zend_Http_Client::setRawData to send this kind of content.»
			 * @see Zend_Http_Client::_prepareBody()
			 * http://magento-forum.ru/topic/4100/
			 */
			->setRawData(
				http_build_query(['xml' => $requestXml], '', '&')
				,'application/x-www-form-urlencoded; charset=utf-8'
			)
			->setMethod(\Zend_Http_Client::POST)
			->setUri($this->getUri())
			->setConfig(array('timeout' => 3))
		;
		return $result;
	});}

	/** @return RegResponse */
	private function regResponse() {return dfc($this, function() {
		/** @var RegResponse $result */
		$result = RegResponse::i();
		$result->loadFromPaymentInfo($this->payment());
		return $result;
	});}
}