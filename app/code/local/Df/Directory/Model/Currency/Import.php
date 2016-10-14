<?php
abstract class Df_Directory_Model_Currency_Import extends Mage_Directory_Model_Currency_Import_Abstract {
	/**
	 * @abstract
	 * @param string $currencyFrom
	 * @param string $currencyTo
	 * @return float
	 * @throws Exception
	 */
	abstract protected function convertInternal($currencyFrom, $currencyTo);

	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getName();

	/**
	 * @override
	 * @return array
	 */
	public function getMessages() {return $this->_messages;}

	/** @var string[] */
	private $_messages = array();

	/**
	 * @override
	 *
	 * @param string $currencyFrom
	 * @param string $currencyTo
	 * @return float|null
	 */
	protected function _convert($currencyFrom, $currencyTo) {
		/** @var float|null $result */
		$result = null;
		try {
			$result = round($this->convertInternal($currencyFrom, $currencyTo), 4);
		}
		catch (Exception $e) {
			$this->_messages[]= rm_ets($e) ? rm_ets($e) : df_t()->nl2br(df_exception_get_trace($e));
			df_handle_entry_point_exception($e, $rethrow = false);
		}
		return $result;
	}

	/**
	 * @param string $currencyCodeFrom
	 * @param string $currencyCodeTo
	 * @throws Exception
	 * @throws Zend_Currency_Exception
	 */
	protected function throwNoRate($currencyCodeFrom, $currencyCodeTo) {
		df_error(
			'Система не в состоянии узнать курс обмена валюты «%s» на валюту «%s» от сервиса «%s»'
			,rm_currency_name($currencyCodeFrom)
			,rm_currency_name($currencyCodeTo)
			,$this->getName()
		);
	}

	/**
	 * @param string $url
	 * @throws Exception
	 */
	protected function throwServiceFailure($url) {
		df_error(Mage::helper('directory')->__('Cannot retrieve rate from %s.', $url));
	}
}