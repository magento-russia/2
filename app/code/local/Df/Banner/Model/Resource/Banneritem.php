<?php
class Df_Banner_Model_Resource_Banneritem extends Df_Core_Model_Resource {
	/**
	 * Нельзя вызывать @see parent::_construct(),
	 * потому что это метод в родительском классе — абстрактный.
	 * @see Mage_Core_Model_Mysql4_Abstract::_construct()
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_init(self::TABLE, Df_Banner_Model_BannerItem::P__ID);}
	/**
	 * @used-by _construct()
	 * @used-by Df_Banner_Model_Resource_Banneritem::_process()
	 */
	const TABLE = 'df_banner/banneritem';
	/**
	 * @used-by Df_Banner_Model_Banneritem::_getResource()
	 * @used-by Df_Banner_Model_Resource_Banneritem_Collection::getResource()
	 * @return Df_Banner_Model_Resource_Banneritem
	 */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}