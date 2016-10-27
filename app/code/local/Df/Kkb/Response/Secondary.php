<?php
namespace Df\Kkb\Response;
class Secondary extends \Df\Kkb\Response {
	/**
	 * @override
	 * @return array(string => string)
	 */
	public function getReportAsArray() {return dfc($this, function() {return array_filter([
		'Операция выполнена успешно?' => df_bts_r($this->isSuccessful())
		,'Диагностическое сообщение' => $this->getErrorMessage()
		,'Код результата авторизации' => $this->onFail($this->getCode())
	]);});}

	/**
	 * @override
	 * @return string
	 */
	public function getTransactionType() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				\Df\Kkb\RequestDocument\Secondary::convertTransactionCodeToMagentoFormat(
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
	 * @return \Df\Kkb\Response\Secondary
	 */
	public static function i($xml = null) {return new self(array(self::P__XML => $xml));}
}