<?php
class Df_Localization_Onetime_Dictionary_Filesystem_Operations extends Df_Core_Xml_Parser_Collection {
	/**
	 * @override
	 * @see Df_Core_Xml_Parser_Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return Df_Localization_Onetime_Dictionary_Filesystem_Operation::_C;}

	/**
	 * @override
	 * @see Df_Core_Xml_Parser_Collection::itemPath()
	 * @return string|string[]
	 */
	protected function itemPath() {return 'filesystem/operation';}

	/**
	 * @static
	 * @param Df_Core_Sxe $e
	 * @return Df_Localization_Onetime_Dictionary_Filesystem_Operations
	 */
	public static function i(Df_Core_Sxe $e) {return new self(array(self::$P__E => $e));}
}