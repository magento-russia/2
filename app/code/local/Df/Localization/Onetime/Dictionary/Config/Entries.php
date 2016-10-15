<?php
class Df_Localization_Onetime_Dictionary_Config_Entries extends \Df\Xml\Parser\Collection {
	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return Df_Localization_Onetime_Dictionary_Config_Entry::class;}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemPath()
	 * @return string|string[]
	 */
	protected function itemPath() {return 'config/entry';}

	/**
	 * @static
	 * @param \Df\Xml\X $e
	 * @return Df_Localization_Onetime_Dictionary_Config_Entries
	 */
	public static function i(\Df\Xml\X $e) {return new self(array(self::$P__E => $e));}
}