<?php
class Df_Banner_Model_Resource_Banner extends Df_Core_Model_Resource {
	/**
	 * Нельзя вызывать @see parent::_construct(),
	 * потому что это метод в родительском классе — абстрактный.
	 * @see Mage_Core_Model_Mysql4_Abstract::_construct()
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_init(self::TABLE, Df_Banner_Model_Banner::P__ID);}
	/**
	 * @used-by _construct()
	 * @used-by Df_Banner_Setup_0_1_1::_process()
	 */
	const TABLE = 'df_banner/banner';
	/**
	 * @used-by Df_Banner_Model_Banner::_getResource()
	 * @used-by Df_Banner_Model_Resource_Banner_Collection::getResource()
	 * @return Df_Banner_Model_Resource_Banner
	 */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}