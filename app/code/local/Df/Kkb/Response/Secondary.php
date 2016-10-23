<?php
class Df_Kkb_Response_Secondary extends Df_Kkb_Response {
	/**
	 * @override
	 * @return array(string => string)
	 */
	public function getReportAsArray() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_clean(array(
				'Операция выполнена успешно?' => df_bts_r($this->isSuccessful())
				,'Диагностическое сообщение' => $this->getErrorMessage()
				,'Код результата авторизации' => $this->onFail($this->getCode())
			));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	public function getTransactionType() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Kkb_RequestDocument_Secondary::convertTransactionCodeToMagentoFormat(
					$this->getElement('bank/merchant/command')->getAttribute('type')
				)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string|null
	 */
	protected function getErrorMessage() {return $this->isSuccessful() ? null : $this->getMessage();}

	/**
	 * @override
	 * @return bool
	 */
	protected function isSuccessful() {return '00' === $this->getCode();}

	/** @return string */
	private function getCode() {return $this->getElementResponse()->getAttribute('code');}
	
	/** @return \Df\Xml\X */
	private function getElementResponse() {return $this->getElement('bank/response');}

	/** @return string */
	private function getMessage() {return $this->getElementResponse()->getAttribute('message');}


	/**
	 * @static
	 * @param string $xml [optional]
	 * @return Df_Kkb_Response_Secondary
	 */
	public static function i($xml = null) {return new self(array(self::P__XML => $xml));}
}