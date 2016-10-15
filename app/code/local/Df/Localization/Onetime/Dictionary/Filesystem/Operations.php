<?php
class Df_Localization_Onetime_Dictionary_Filesystem_Operations extends \Df\Xml\Parser\Collection {
	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return Df_Localization_Onetime_Dictionary_Filesystem_Operation::_C;}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemPath()
	 * @return string|string[]
	 */
	protected function itemPath() {return 'filesystem/operation';}

	/**
	 * @static
	 * @param \Df\Xml\X $e
	 * @return Df_Localization_Onetime_Dictionary_Filesystem_Operations
	 */
	public static function i(\Df\Xml\X $e) {return new self(array(self::$P__E => $e));}
}