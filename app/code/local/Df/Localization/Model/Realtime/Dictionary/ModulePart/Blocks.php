<?php
class Df_Localization_Model_Realtime_Dictionary_ModulePart_Blocks
	extends Df_Core_Model_SimpleXml_Parser_Collection {
	/**
	 * @override
	 * @return string
	 */
	protected function getItemClass() {
		return Df_Localization_Model_Realtime_Dictionary_ModulePart_Block::_CLASS;
	}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getItemsXmlPathAsArray() {return array('block');}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param Df_Varien_Simplexml_Element $element
	 * @return Df_Localization_Model_Realtime_Dictionary_ModulePart_Blocks
	 */
	public static function i(Df_Varien_Simplexml_Element $element) {
		return new self(array(self::P__SIMPLE_XML => $element));
	}
}