<?php
class Df_Localization_Model_Onetime_Dictionary_Db_Columns
	extends Df_Core_Model_SimpleXml_Parser_Collection {
	/**
	 * @override
	 * @return string
	 */
	protected function getItemClass() {
		return Df_Localization_Model_Onetime_Dictionary_Db_Column::_CLASS;
	}

	/**
	 * @override
	 * @see Df_Core_Model_SimpleXml_Parser_Collection:getItemParamsAdditional()
	 * @return array(string => mixed)
	 */
	protected function getItemParamsAdditional() {return array(
		Df_Localization_Model_Onetime_Dictionary_Db_Column::P__TABLE => $this->getTable()
	);}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getItemsXmlPathAsArray() {return array('column');}

	/** @return Df_Localization_Model_Onetime_Dictionary_Db_Table */
	private function getTable() {return $this[self::$P__TABLE];}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__TABLE, Df_Localization_Model_Onetime_Dictionary_Db_Table::_CLASS);
	}
	const _CLASS = __CLASS__;
	/** @var string */
	private static $P__TABLE = 'table';
	/**
	 * @static
	 * @param Df_Varien_Simplexml_Element $element
	 * @param Df_Localization_Model_Onetime_Dictionary_Db_Table $table
	 * @return Df_Localization_Model_Onetime_Dictionary_Db_Columns
	 */
	public static function i(
		Df_Varien_Simplexml_Element $element
		,Df_Localization_Model_Onetime_Dictionary_Db_Table $table
	) {
		return new self(array(self::P__SIMPLE_XML => $element, self::$P__TABLE => $table));
	}
}