<?php
class Df_PromoGift_Model_Resource_Gift extends Df_Core_Model_Resource {
	/**
	 * Нельзя вызывать @see parent::_construct(),
	 * потому что это метод в родительском классе — абстрактный.
	 * @see Mage_Core_Model_Resource_Abstract::_construct()
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_init(self::TABLE, Df_PromoGift_Model_Gift::P__ID);}
	/**
	 * @used-by Df_PromoGift_Model_Resource_Indexer::_construct()
	 * @used-by Df_PromoGift_Setup_1_0_0::_process()
	 */
	const TABLE = 'df_promo_gift/gift';
	/** @return Df_PromoGift_Model_Resource_Gift */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}