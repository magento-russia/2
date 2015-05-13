<?php
class Df_Kkb_Model_RequestDocument_Registration extends Df_Kkb_Model_RequestDocument_Signed {
	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getLetterAttributes() {
		return array(
			'cert_id' => $this->getServiceConfig()->getCertificateId()
			,'name' => $this->getServiceConfig()->getShopName()
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
			Df_Varien_Simplexml_Element::KEY__ATTRIBUTES =>
				array(
					'merchant_id' => $this->getServiceConfig()->getShopId()
					,'amount' => $this->getAmount()
				)
			,Df_Varien_Simplexml_Element::KEY__VALUE => null
		);
	}

	/** @return array(string => mixed) */
	private function getDocumentData_Order() {
		return array(
			Df_Varien_Simplexml_Element::KEY__ATTRIBUTES =>
				array(
					'order_id' => $this->getOrderId()
					,'amount' => $this->getAmount()
					,'currency' => $this->getCurrencyCode()
				)
			,Df_Varien_Simplexml_Element::KEY__VALUE =>
				array('department' => $this->getDocumentData_Department())
		);
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param Df_Kkb_Model_Request_Payment $requestPayment
	 * @return Df_Kkb_Model_RequestDocument_Registration
	 */
	public static function i(Df_Kkb_Model_Request_Payment $requestPayment) {
		return new self(array(self::P__REQUEST => $requestPayment));
	}
}