<?php
class Df_Localization_Onetime_Dictionary_Db_Columns extends \Df\Xml\Parser\Collection {
	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return Df_Localization_Onetime_Dictionary_Db_Column::_C;}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection:itemParams()
	 * @return array(string => mixed)
	 */
	protected function itemParams() {return array(
		Df_Localization_Onetime_Dictionary_Db_Column::P__TABLE => $this[self::$P__TABLE]
	);}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemPath()
	 * @return string
	 */
	protected function itemPath() {return 'column';}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__TABLE, Df_Localization_Onetime_Dictionary_Db_Table::_C);
	}
	/** @var string */
	private static $P__TABLE = 'table';

	/**
	 * @static
	 * @param \Df\Xml\X $e
	 * @param Df_Localization_Onetime_Dictionary_Db_Table $table
	 * @return Df_Localization_Onetime_Dictionary_Db_Columns
	 */
	public static function i(
		\Df\Xml\X $e, Df_Localization_Onetime_Dictionary_Db_Table $table
	) {return new self(array(self::$P__E => $e, self::$P__TABLE => $table));}
}