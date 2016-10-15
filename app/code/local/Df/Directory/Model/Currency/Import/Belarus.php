<?php
class Df_Directory_Model_Currency_Import_Belarus extends Df_Directory_Model_Currency_Import_XmlStandard {
	/**
	 * @protected
	 * @return string
	 */
	protected function getBaseCurrencyCode() {return Df_Directory_Model_Currency::BYR;}
	/**
	 * @override
	 * @return string
	 */
	protected function getName() {return 'Национальный банк Республики Беларусь';}
	/**
	 * @override
	 * @return string
	 */
	protected function getTagName_CurrencyCode() {return 'CharCode';}
	/**
	 * @override
	 * @return string
	 */
	protected function getTagName_CurrencyItem() {return 'Currency';}
	/**
	 * @override
	 * @return string
	 */
	protected function getTagName_Denominator() {return 'Scale';}
	/**
	 * @override
	 * @return string
	 */
	protected function getTagName_Rate() {return 'Rate';}
	/**
	 * @override
	 * @return string
	 */
	protected function getUrl() {return 'http://www.nbrb.by/Services/XmlExRates.aspx';}

}