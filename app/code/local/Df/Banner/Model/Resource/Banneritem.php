<?php
class Df_Banner_Model_Resource_Banneritem extends Mage_Core_Model_Mysql4_Abstract {
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
		$this->_init(self::TABLE_NAME, Df_Banner_Model_BannerItem::P__ID);
	}
	const _CLASS = __CLASS__;
	const TABLE_NAME = 'df_banner/banneritem';
	/**
	 * @see Df_Banner_Model_Banneritem::_construct()
	 * @see Df_Banner_Model_Resource_Banneritem_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Banner_Model_Resource_Banneritem */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}