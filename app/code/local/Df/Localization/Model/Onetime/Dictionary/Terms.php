<?php
class Df_Localization_Model_Onetime_Dictionary_Terms extends Df_Core_Model_SimpleXml_Parser_Collection {
	/**
	 * @override
	 * @return string
	 */
	protected function getItemClass() {return Df_Localization_Model_Onetime_Dictionary_Term::_CLASS;}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getItemsXmlPathAsArray() {return array('term');}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param Df_Varien_Simplexml_Element $element
	 * @return Df_Localization_Model_Onetime_Dictionary_Terms
	 */
	public static function i(Df_Varien_Simplexml_Element $element) {
		return new self(array(self::P__SIMPLE_XML => $element));
	}
}