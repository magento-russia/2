<?php
class Df_Catalog_Model_Product_Option_Title extends Df_Core_Model {
	/**
	 * @override
	 * @return Df_Catalog_Model_Resource_Product_Option_Title_Collection
	 */
	public function getResourceCollection() {return self::c();}

	/**
	 * @override
	 * @return Df_Catalog_Model_Resource_Product_Option_Title
	 */
	protected function _getResource() {return Df_Catalog_Model_Resource_Product_Option_Title::s();}

	/**
	 * @used-by Df_Catalog_Model_Resource_Product_Option_Title_Collection::_construct()
	 * @used-by Df_Localization_Onetime_Dictionary_Rule_Conditions_Product_Option::getEntityClass()
	 */
	const _C = __CLASS__;
	const P__ID = 'option_title_id';
	const P__TITLE = 'title';

	/** @return Df_Catalog_Model_Resource_Product_Option_Title_Collection */
	public static function c() {return new Df_Catalog_Model_Resource_Product_Option_Title_Collection;}
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
	/** @return Df_Catalog_Model_Product_Option_Title */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}