<?php
class Df_1C_Model_Cml2_Import_Data_Collection_Products
	extends Df_1C_Model_Cml2_Import_Data_Collection {
	/**
	 * @override
	 * @return string
	 */
	protected function getItemClass() {
		return Df_1C_Model_Cml2_Import_Data_Entity_Product::_CLASS;
	}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getItemsXmlPathAsArray() {
		return
			array(
				''
				,'КоммерческаяИнформация'
				,'Каталог'
				,'Товары'
				,'Товар'
			)
		;
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param Df_Varien_Simplexml_Element $xml
	 * @return Df_1C_Model_Cml2_Import_Data_Collection_Products
	 */
	public static function i(Df_Varien_Simplexml_Element $xml) {
		return new self(array(self::P__SIMPLE_XML => $xml));
	}
}