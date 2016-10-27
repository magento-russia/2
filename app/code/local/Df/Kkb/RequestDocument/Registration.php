<?php
namespace Df\Kkb\RequestDocument;
use Df\Xml\X as X;
class Registration extends Signed {
	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getLetterAttributes() {return [
		'cert_id' => $this->configS()->getCertificateId()
		,'name' => $this->configS()->getShopName()
	];}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getLetterBody() {return ['order' => $this->getDocumentData_Order()];}

	/** @return array(string => mixed) */
	private function getDocumentData_Department() {return [
		X::ATTR => ['merchant_id' => $this->configS()->getShopId(), 'amount' => $this->amount()]
		,X::CONTENT => null
	];}

	/** @return array(string => mixed) */
	private function getDocumentData_Order() {return [
		X::ATTR => [
			'order_id' => $this->orderIId()
			,'amount' => $this->amount()
			,'currency' => $this->getCurrencyCode()
		]
		,X::CONTENT => ['department' => $this->getDocumentData_Department()]
	];}


	/**
	 * @static
	 * @param \Df\Kkb\Request\Payment $requestPayment
	 * @return \Df\Kkb\RequestDocument\Registration
	 */
	public static function i(\Df\Kkb\Request\Payment $requestPayment) {
		return new self([self::P__REQUEST => $requestPayment]);
	}
}