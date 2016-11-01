<?php
class Df_C1_Cml2_Import_Data_Collection_Categories extends Df_C1_Cml2_Import_Data_Collection {
	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return Df_C1_Cml2_Import_Data_Entity_Category::class;}


	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemPath()
	 * @return string|string[]
	 */
	protected function itemPath() {return $this->cfg(self::$P__XML_PATH_AS_ARRAY, 'Группы/Группа');}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__XML_PATH_AS_ARRAY, DF_V_ARRAY, false);
	}
	/** @var string */
	private static $P__XML_PATH_AS_ARRAY = 'xml_path_as_array';
	/**
	 * @used-by Df_C1_Cml2_Import_Data_Entity_Category::getChildren()
	 * @used-by Df_C1_Cml2_State_Import_Collections::getCategories()
	 * @static
	 * @param \Df\Xml\X $xml
	 * @param array|null $pathAsArray [optional]
	 * @return Df_C1_Cml2_Import_Data_Collection_Categories
	 */
	public static function i(\Df\Xml\X $xml, $pathAsArray = null) {
		return new self(array(self::$P__E => $xml, self::$P__XML_PATH_AS_ARRAY => $pathAsArray));
	}
}