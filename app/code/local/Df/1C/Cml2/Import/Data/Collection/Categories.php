<?php
class Df_1C_Cml2_Import_Data_Collection_Categories extends Df_1C_Cml2_Import_Data_Collection {
	/**
	 * @override
	 * @see Df_Core_Xml_Parser_Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return Df_1C_Cml2_Import_Data_Entity_Category::_C;}


	/**
	 * @override
	 * @see Df_Core_Xml_Parser_Collection::itemPath()
	 * @return string|string[]
	 */
	protected function itemPath() {return $this->cfg(self::$P__XML_PATH_AS_ARRAY, 'Группы/Группа');}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__XML_PATH_AS_ARRAY, RM_V_ARRAY, false);
	}
	/** @var string */
	private static $P__XML_PATH_AS_ARRAY = 'xml_path_as_array';
	/**
	 * @used-by Df_1C_Cml2_Import_Data_Entity_Category::getChildren()
	 * @used-by Df_1C_Cml2_State_Import_Collections::getCategories()
	 * @static
	 * @param Df_Core_Sxe $xml
	 * @param array|null $pathAsArray [optional]
	 * @return Df_1C_Cml2_Import_Data_Collection_Categories
	 */
	public static function i(Df_Core_Sxe $xml, $pathAsArray = null) {
		return new self(array(self::$P__E => $xml, self::$P__XML_PATH_AS_ARRAY => $pathAsArray));
	}
}