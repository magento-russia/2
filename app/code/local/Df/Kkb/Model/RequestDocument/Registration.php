<?php
class Df_Kkb_Model_RequestDocument_Registration extends Df_Kkb_Model_RequestDocument_Signed {
	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getLetterAttributes() {
		return array(
			'cert_id' => $this->configS()->getCertificateId()
			,'name' => $this->configS()->getShopName()
		);
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getLetterBody() {return array('order' => $this->getDocumentData_Order());}

	/** @return array(string => mixed) */
	private function getDocumentData_Department() {
		return array(
			\Df\Xml\X::ATTR => array(
				'merchant_id' => $this->configS()->getShopId()
				, 'amount' => $this->amount()
			)
			,\Df\Xml\X::CONTENT => null
		);
	}

	/** @return array(string => mixed) */
	private function getDocumentData_Order() {
		return array(
			\Df\Xml\X::ATTR => array(
				'order_id' => $this->orderIId()
				,'amount' => $this->amount()
				,'currency' => $this->getCurrencyCode()
			)
			,\Df\Xml\X::CONTENT => array('department' => $this->getDocumentData_Department())
		);
	}


	/**
	 * @static
	 * @param Df_Kkb_Model_Request_Payment $requestPayment
	 * @return Df_Kkb_Model_RequestDocument_Registration
	 */
	public static function i(Df_Kkb_Model_Request_Payment $requestPayment) {
		return new self(array(self::P__REQUEST => $requestPayment));
	}
}