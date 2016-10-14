<?php
class Df_Localization_Realtime_Dictionary_ModulePart_Terms extends Df_Core_Xml_Parser_Collection {
	/**
	 * @override
	 * @see Df_Core_Xml_Parser_Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return Df_Localization_Realtime_Dictionary_ModulePart_Term::_C;}

	/**
	 * @override
	 * @see Df_Core_Xml_Parser_Collection::itemPath()
	 * @return string|string[]
	 */
	protected function itemPath() {return 'term';}

	/**
	 * @static
	 * @used-by Df_Localization_Realtime_Dictionary_Layout::terms()
	 * @used-by Df_Localization_Realtime_Dictionary_ModulePart_Block::terms()
	 * @used-by Df_Localization_Realtime_Dictionary_ModulePart_Controller::terms()
	 * @param Df_Core_Sxe $e
	 * @return Df_Localization_Realtime_Dictionary_ModulePart_Terms
	 */
	public static function i(Df_Core_Sxe $e) {return new self(array(self::$P__E => $e));}
}