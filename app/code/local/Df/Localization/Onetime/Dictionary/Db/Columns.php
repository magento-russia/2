<?php
class Df_Localization_Onetime_Dictionary_Db_Columns extends Df_Core_Xml_Parser_Collection {
	/**
	 * @override
	 * @see Df_Core_Xml_Parser_Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return Df_Localization_Onetime_Dictionary_Db_Column::_C;}

	/**
	 * @override
	 * @see Df_Core_Xml_Parser_Collection:itemParams()
	 * @return array(string => mixed)
	 */
	protected function itemParams() {return array(
		Df_Localization_Onetime_Dictionary_Db_Column::P__TABLE => $this[self::$P__TABLE]
	);}

	/**
	 * @override
	 * @see Df_Core_Xml_Parser_Collection::itemPath()
	 * @return string
	 */
	protected function itemPath() {return 'column';}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__TABLE, Df_Localization_Onetime_Dictionary_Db_Table::_C);
	}
	/** @var string */
	private static $P__TABLE = 'table';

	/**
	 * @static
	 * @param Df_Core_Sxe $e
	 * @param Df_Localization_Onetime_Dictionary_Db_Table $table
	 * @return Df_Localization_Onetime_Dictionary_Db_Columns
	 */
	public static function i(
		Df_Core_Sxe $e, Df_Localization_Onetime_Dictionary_Db_Table $table
	) {return new self(array(self::$P__E => $e, self::$P__TABLE => $table));}
}