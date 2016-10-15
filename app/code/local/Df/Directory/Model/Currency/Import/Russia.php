<?php
class Df_Directory_Model_Currency_Import_Russia extends Df_Directory_Model_Currency_Import_XmlStandard {
	/**
	 * @protected
	 * @return string
	 */
	protected function getBaseCurrencyCode() {return Df_Directory_Model_Currency::RUB;}
	/**
	 * @override
	 * @return string
	 */
	protected function getName() {return 'Банк России';}
	/**
	 * @override
	 * @return string
	 */
	protected function getTagName_CurrencyCode() {return 'CharCode';}
	/**
	 * @override
	 * @return string
	 */
	protected function getTagName_CurrencyItem() {return 'Valute';}
	/**
	 * @override
	 * @return string
	 */
	protected function getTagName_Denominator() {
		return 'Nominal';
	}
	/**
	 * @override
	 * @return string
	 */
	protected function getTagName_Rate() {return 'Value';}

	/**
	 * @override
	 * @return string
	 */
	protected function getUrl() {return 'http://www.cbr.ru/scripts/XML_daily.asp';}

}