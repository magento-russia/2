<?php
class Df_Chronopay_Model_Gate_Response extends Df_Core_Model_SimpleXml_Parser_Entity {
	/** @return Df_Chronopay_Model_Gate_Response */
	public function check() {
		if (0 != $this->getCode()) {
			df_error($this->getDiagnosticMessage());
		}
		return $this;
	}

	/** @return int */
	public function getCode() {return $this->descendI('code');}

	/** @return string */
	public function getDiagnosticMessage() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = implode("\r\n", array(
				'Error in capturing the payment via ChronoPay.'
				,'ChronoPay error code: ' . $this->getCode()
				, 'ChronoPay extended error code: ' . $this->getExtendedCode()
				, 'ChronoPay error message: ' . $this->getMessage()
				, 'ChronoPay extended error message: ' . $this->getExtendedMessage()
				, 'Transaction ID: ' . $this->getTransactionId()
			));
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getExtendedCode() {return $this->descendS('Extended/code');}

	/** @return string */
	public function getExtendedMessage() {return $this->descendS('Extended/message');}

	/** @return string */
	public function getMessage() {return $this->descendS('message');}

	/** @return int */
	public function getTransactionId() {return $this->descendI('Transaction');}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param string $xml
	 * @return Df_Chronopay_Model_Gate_Response
	 */
	public static function i($xml) {return new self(array(self::P__SIMPLE_XML => $xml));}
}