<?php
class Df_Chronopay_Block_Gate_Response extends Df_Core_Block_Template_NoCache {
	/** @return array(string => string) */
	public function getItems() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array(
				'ChronoPay error code' => $this->getResponse()->getCode()
				,'ChronoPay error message'  => $this->getResponse()->getMessage()
				,'Transaction ID'  => $this->getResponse()->getTransactionId()
				,'ChronoPay extended error code'  => $this->getResponse()->getExtendedCode()
				,'ChronoPay extended error message'  => $this->getResponse()->getExtendedMessage()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Chronopay_Model_Gate_Response */
	public function getResponse() {return $this->cfg(self::P__RESPONSE);}

	const _CLASS = __CLASS__;
	const P__RESPONSE = 'response';
	/**
	 * @param Df_Chronopay_Model_Gate_Response $response
	 * @return Df_Chronopay_Block_Gate_Response
	 */
	public static function i(Df_Chronopay_Model_Gate_Response $response) {
		return df_block(new self(array(self::P__RESPONSE => $response)));
	}
}