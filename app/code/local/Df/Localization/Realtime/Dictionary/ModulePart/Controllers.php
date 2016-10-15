<?php
class Df_Localization_Realtime_Dictionary_ModulePart_Controllers extends \Df\Xml\Parser\Collection {
	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return Df_Localization_Realtime_Dictionary_ModulePart_Controller::_C;}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemPath()
	 * @return string|string[]
	 */
	protected function itemPath() {return 'controller';}

	/**
	 * @static
	 * @param \Df\Xml\X $e
	 * @return Df_Localization_Realtime_Dictionary_ModulePart_Controllers
	 */
	public static function i(\Df\Xml\X $e) {return new self(array(self::$P__E => $e));}
}