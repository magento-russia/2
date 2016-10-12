<?php
class Df_Catalog_Model_Product_Option_Title extends Df_Core_Model {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Catalog_Model_Resource_Product_Option_Title::mf());
	}

	const _CLASS = __CLASS__;
	const P__ID = 'option_title_id';
	const P__TITLE = 'title';

	/** @return Df_Catalog_Model_Resource_Product_Option_Title_Collection */
	public static function c() {return self::s()->getCollection();}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Catalog_Model_Product_Option_Title
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_Catalog_Model_Product_Option_Title
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
	/**
	 * @see Df_Catalog_Model_Resource_Product_Option_Title::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
	/** @return Df_Catalog_Model_Product_Option_Title */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}