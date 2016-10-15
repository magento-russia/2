<?php
class Df_Localization_Realtime_Dictionary_Modules extends \Df\Xml\Parser\Collection {
	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return Df_Localization_Realtime_Dictionary_Module::class;}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemPath()
	 * @return string|string[]
	 */
	protected function itemPath() {return 'module';}

	/**
	 * @static
	 * @param \Df\Xml\X $e
	 * @return Df_Localization_Realtime_Dictionary_Modules
	 */
	public static function i(\Df\Xml\X $e) {return new self(array(self::$P__E => $e));}
}