<?php
class Df_Localization_Realtime_Dictionary_ModulePart_Terms extends \Df\Xml\Parser\Collection {
	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return Df_Localization_Realtime_Dictionary_ModulePart_Term::_C;}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemPath()
	 * @return string|string[]
	 */
	protected function itemPath() {return 'term';}

	/**
	 * @static
	 * @used-by Df_Localization_Realtime_Dictionary_Layout::terms()
	 * @used-by Df_Localization_Realtime_Dictionary_ModulePart_Block::terms()
	 * @used-by Df_Localization_Realtime_Dictionary_ModulePart_Controller::terms()
	 * @param \Df\Xml\X $e
	 * @return Df_Localization_Realtime_Dictionary_ModulePart_Terms
	 */
	public static function i(\Df\Xml\X $e) {return new self(array(self::$P__E => $e));}
}