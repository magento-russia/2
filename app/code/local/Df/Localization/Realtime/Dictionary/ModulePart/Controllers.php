<?php
class Df_Localization_Realtime_Dictionary_ModulePart_Controllers extends Df_Core_Xml_Parser_Collection {
	/**
	 * @override
	 * @see Df_Core_Xml_Parser_Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return Df_Localization_Realtime_Dictionary_ModulePart_Controller::_C;}

	/**
	 * @override
	 * @see Df_Core_Xml_Parser_Collection::itemPath()
	 * @return string|string[]
	 */
	protected function itemPath() {return 'controller';}

	/**
	 * @static
	 * @param Df_Core_Sxe $e
	 * @return Df_Localization_Realtime_Dictionary_ModulePart_Controllers
	 */
	public static function i(Df_Core_Sxe $e) {return new self(array(self::$P__E => $e));}
}