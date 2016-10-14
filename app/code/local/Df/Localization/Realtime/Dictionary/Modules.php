<?php
class Df_Localization_Realtime_Dictionary_Modules extends Df_Core_Xml_Parser_Collection {
	/**
	 * @override
	 * @see Df_Core_Xml_Parser_Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return Df_Localization_Realtime_Dictionary_Module::_C;}

	/**
	 * @override
	 * @see Df_Core_Xml_Parser_Collection::itemPath()
	 * @return string|string[]
	 */
	protected function itemPath() {return 'module';}

	/**
	 * @static
	 * @param Df_Core_Sxe $e
	 * @return Df_Localization_Realtime_Dictionary_Modules
	 */
	public static function i(Df_Core_Sxe $e) {return new self(array(self::$P__E => $e));}
}