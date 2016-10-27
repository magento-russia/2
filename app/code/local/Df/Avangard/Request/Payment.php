<?php
namespace Df\Avangard\Request;
use Df\Avangard\RequestDocument as RegRequest;
use Df\Avangard\Response\Registration as RegResponse;
use Mage_Sales_Model_Order_Payment_Transaction as T;
class Payment extends \Df\Payment\Request\Payment {
	/**
	 * @override
	 * @return string
	 */
	public function getTransactionType() {return T::TYPE_PAYMENT;}

	/**
	 * @override
	 * @see \Df\Payment\Request\Payment::_params()
	 * @used-by \Df\Payment\Request\Payment::params()
	 * @return array(string => string|int)
	 */
	protected function _params() {return ['ticket' => $this->regResponse()->getPaymentExternalId()];}

	/** @return RegRequest */
	private function regRequest() {return dfc($this, function() {
		/** @var RegRequest $result */
		$result = RegRequest::registration([
			'shop_id' => $this->shopId()
			,'shop_passwd' => $this->password()
			,'amount' => df_round(100 * $this->amount()->getAsFixedFloat())
			,'order_number' => $this->orderIId()
			,'order_description' => $this->description()
			,'language' => 'RU'
			,'back_url' => $this->urlCustomerReturn()
			,'client_name' => $this->getCustomerNameFull()
			,'client_address' => $this->street()
			,'client_phone' => $this->phone()
			,'client_email' => $this->email()
			,'client_ip' => $this->getCustomerIpAddress()
		]);
		df_report('registration-request-{date}-{time}.xml', $result->getXml());
		return $result;
	});}

	/** @return RegResponse */
	private function regResponse() {return dfc($this, function() {
		/** @var string $xml */
		$xml = $this->regResponseRaw();
		df_report('registration-{date}-{time}.xml', $xml);
		/** @var RegResponse $result */
		$result = RegResponse::i(df_xml_parse($xml)->asCanonicalArray());
		$result->postProcess($this->payment());
		return $result;
	});}

	/** @return string */
	private function regResponseRaw() {
		/** @var \Zend_Http_Client $c */
		$c = new \Zend_Http_Client();
		$c
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
				http_build_query(['xml' => $this->regRequest()->getXml()], '', '&')
				,'application/x-www-form-urlencoded; charset=utf-8'
			)
			->setMethod(\Zend_Http_Client::POST)
			->setUri('https://www.avangard.ru/iacq/h2h/reg')
			->setConfig(['timeout' => 3])
		;
		return $c->request()->getBody();
	}
}