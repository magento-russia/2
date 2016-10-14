<?php
/** @method Df_LiqPay_Model_Payment getMethod() */
class Df_LiqPay_Model_Request_Payment extends Df_Payment_Model_Request_Payment {
	/**
	 * @override
	 * @see Df_Payment_Model_Request_Payment::_params()
	 * @used-by Df_Payment_Model_Request_Payment::params()
	 * @return array(string => string|int)
	 */
	protected function _params() {
		return array(
			'operation_xml' => base64_encode($this->xml())
			, 'signature' => base64_encode(sha1($this->password() . $this->xml() . $this->password(), 1))
		);
	}

	/** @return array(string => string) */
	private function getParamsForXml() {
		/** @var array(string => string) $result */
		$result = array(
			'version' => '1.2'
			,'default_phone' =>
				'+' . Df_Core_Model_Format_MobilePhoneNumber::i($this->phone())->getOnlyDigits()
			,'amount' => $this->amountS()
			// Раньше LiqPay запрещал кириллицу в описании платежа,
			// но теперь, вроде, разрешает.
			,'description' => $this->getTransactionDescription()
			,'currency' => $this->currencyCode()
			,'order_id' => $this->orderIId()
			,'merchant_id' => $this->shopId()
			,'server_url' => $this->urlConfirm()
			// iPay и LiqPay, в отличие от других платёжных систем,
			// не поддерживают разные веб-адреса для успешного и неуспешного сценариев оплаты
			,'result_url'=> $this->urlReturn()
		);
		if ($this->getMethod()->getSubmethod()) {
			$result['pay_way'] = $this->getMethod()->getSubmethod();
		}
		return $result;
	}

	/**
	 * Без _nosid система будет формировать ссылку c ?___SID=U.
	 * На всякий случай избегаем этого.
	 * @return string
	 */
	private function urlReturn() {
		return Mage::getUrl($this->getMethod()->getCode() . '/customerReturn', array('_nosid' => true));
	}

	/** @return string */
	private function xml() {
		if (!isset($this->{__METHOD__})) {
			/** @var Varien_Object $object */
			$object = new Varien_Object($this->getParamsForXml());
			$this->{__METHOD__} = $object->toXml(
				// все свойства
				$arrAttributes = array()
				// корневой тэг
				, $rootName = 'request'
				/* не добавлять <?xml version="1.0" encoding="UTF-8"?> */
				, $addOpenTag = false
				// запрещаем добавление CDATA,
				// потому что LiqPay эту синтаксическую конструкцию не понимает
				, $addCdata = false
			);
		}
		return $this->{__METHOD__};
	}
}