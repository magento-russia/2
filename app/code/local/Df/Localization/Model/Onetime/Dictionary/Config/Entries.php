<?php
class Df_Localization_Model_Onetime_Dictionary_Config_Entries
	extends Df_Core_Model_SimpleXml_Parser_Collection {
	/**
	 * @override
	 * @return string
	 */
	protected function getItemClass() {
		return Df_Localization_Model_Onetime_Dictionary_Config_Entry::_CLASS;
	}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getItemsXmlPathAsArray() {return array('config', 'entry');}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param Df_Varien_Simplexml_Element $element
	 * @return Df_Localization_Model_Onetime_Dictionary_Config_Entries
	 */
	public static function i(Df_Varien_Simplexml_Element $element) {
		return new self(array(self::P__SIMPLE_XML => $element));
	}
}