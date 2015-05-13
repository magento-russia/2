<?php
class Df_1C_Model_Cml2_Import_Data_Collection_Categories
	extends Df_1C_Model_Cml2_Import_Data_Collection {
	/**
	 * @override
	 * @return string
	 */
	protected function getItemClass() {return Df_1C_Model_Cml2_Import_Data_Entity_Category::_CLASS;}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getItemsXmlPathAsArray() {
		return $this->cfg(self::P__XML_PATH_AS_ARRAY, array('Группы', 'Группа'));
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__XML_PATH_AS_ARRAY, self::V_ARRAY, false);
	}
	const _CLASS = __CLASS__;
	const P__XML_PATH_AS_ARRAY = 'xml_path_as_array';
	/**
	 * @static
	 * @param Df_Varien_Simplexml_Element $xml
	 * @param array|null $pathAsArray [optional]
	 * @return Df_1C_Model_Cml2_Import_Data_Collection_Categories
	 */
	public static function i(Df_Varien_Simplexml_Element $xml, $pathAsArray = null) {
		return new self(array(
			self::P__SIMPLE_XML => $xml, self::P__XML_PATH_AS_ARRAY => $pathAsArray
		));
	}
}