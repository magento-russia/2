<?php
class Df_Directory_Model_Currency_Import_Kazakhstan extends Df_Directory_Model_Currency_Import_XmlStandard {
	/**
	 * @protected
	 * @return string
	 */
	protected function getBaseCurrencyCode() {return Df_Directory_Model_Currency::KZT;}
	/**
	 * @override
	 * @return string
	 */
	protected function getName() {return 'Национальный банк Казахстана';}
	/**
	 * @override
	 * @return string
	 */
	protected function getTagName_CurrencyCode() {return 'title';}
	/**
	 * @override
	 * @return string
	 */
	protected function getTagName_CurrencyItem() {return 'channel/item';}
	/**
	 * @override
	 * @return string
	 */
	protected function getTagName_Denominator() {return 'quant';}
	/**
	 * @override
	 * @return string
	 */
	protected function getTagName_Rate() {return 'description';}
	/**
	 * @override
	 * @return string
	 */
	protected function getUrl() {return 'http://www.nationalbank.kz/rss/rates_all.xml';}

}