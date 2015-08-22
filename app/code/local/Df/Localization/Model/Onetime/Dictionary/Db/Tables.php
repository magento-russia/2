<?php
class Df_Localization_Model_Onetime_Dictionary_Db_Tables
	extends Df_Core_Model_SimpleXml_Parser_Collection {
	/**
	 * @override
	 * @return string
	 */
	protected function getItemClass() {
		return Df_Localization_Model_Onetime_Dictionary_Db_Table::_CLASS;
	}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getItemsXmlPathAsArray() {return array('db', 'table');}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param Df_Varien_Simplexml_Element $element
	 * @return Df_Localization_Model_Onetime_Dictionary_Db_Tables
	 */
	public static function i(Df_Varien_Simplexml_Element $element) {
		return new self(array(self::P__SIMPLE_XML => $element));
	}
}