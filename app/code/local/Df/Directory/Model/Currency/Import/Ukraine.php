<?php
class Df_Directory_Model_Currency_Import_Ukraine extends Df_Directory_Model_Currency_Import_XmlStandard {
	/**
	 * @protected
	 * @return string
	 */
	protected function getBaseCurrencyCode() {return Df_Directory_Model_Currency::UAH;}
	/**
	 * @override
	 * @return string
	 */
	protected function getName() {return 'Национальный банк Украины';}
	/**
	 * @override
	 * @return string
	 */
	protected function getTagName_CurrencyCode() {return 'char3';}
	/**
	 * @override
	 * @return string
	 */
	protected function getTagName_CurrencyItem() {return 'item';}
	/**
	 * @override
	 * @return string
	 */
	protected function getTagName_Denominator() {return 'size';}
	/**
	 * @override
	 * @return string
	 */
	protected function getTagName_Rate() {return 'rate';}
	/**
	 * @override
	 * @return string
	 */
	protected function getUrl() {return 'http://bank-ua.com/export/currrate.xml';}
	const _CLASS = __CLASS__;
}