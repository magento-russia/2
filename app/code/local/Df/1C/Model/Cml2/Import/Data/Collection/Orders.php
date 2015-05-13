<?php
class Df_1C_Model_Cml2_Import_Data_Collection_Orders
	extends Df_1C_Model_Cml2_Import_Data_Collection {
	/**
	 * @override
	 * @return string
	 */
	protected function getItemClass() {
		return Df_1C_Model_Cml2_Import_Data_Entity_Order::_CLASS;
	}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getItemsXmlPathAsArray() {
		return array('', 'КоммерческаяИнформация', 'Документ');
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param Df_Varien_Simplexml_Element $element
	 * @return Df_1C_Model_Cml2_Import_Data_Collection_Orders
	 */
	public static function i(Df_Varien_Simplexml_Element $element) {
		return new self(array(self::P__SIMPLE_XML => $element));
	}
}