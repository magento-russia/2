<?php
class Df_Catalog_Model_Resource_Product_Option_Title extends Mage_Core_Model_Mysql4_Abstract {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		/**
		 * Нельзя вызывать parent::_construct(),
		 * потому что это метод в родительском классе — абстрактный.
		 * @see Mage_Core_Model_Resource_Abstract::_construct()
		 */
		$this->_init(self::TABLE_NAME, Df_Catalog_Model_Product_Option_Title::P__ID);
	}
	const _CLASS = __CLASS__;
	const TABLE_NAME = 'catalog/product_option_title';
	/**
	 * @see Df_Catalog_Model_Product_Option_Title::_construct()
	 * @see Df_Catalog_Model_Resource_Product_Option_Title_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Catalog_Model_Resource_Product_Option_Title */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}