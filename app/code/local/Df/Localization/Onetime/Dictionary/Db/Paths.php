<?php
class Df_Localization_Onetime_Dictionary_Db_Paths extends Df_Core_Xml_Parser_Collection {
	/**
	 * @override
	 * @see Df_Core_Xml_Parser_Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return Df_Localization_Onetime_Dictionary_Db_Path::_C;}

	/**
	 * @override
	 * @see Df_Core_Xml_Parser_Collection::itemPath()
	 * @return string
	 */
	protected function itemPath() {return 'path';}

	/**
	 * @static
	 * @param Df_Core_Sxe $e
	 * @return Df_Localization_Onetime_Dictionary_Db_Paths
	 */
	public static function i(Df_Core_Sxe $e) {return new self(array(self::$P__E => $e));}
}