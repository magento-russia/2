<?php
class Df_Localization_Onetime_Dictionary_Db_Tables extends \Df\Xml\Parser\Collection {
	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return Df_Localization_Onetime_Dictionary_Db_Table::class;}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemPath()
	 * @return string
	 */
	protected function itemPath() {return 'db/table';}

	/**
	 * @static
	 * @param \Df\Xml\X $e
	 * @return Df_Localization_Onetime_Dictionary_Db_Tables
	 */
	public static function i(\Df\Xml\X $e) {return new self(array(self::$P__E => $e));}
}