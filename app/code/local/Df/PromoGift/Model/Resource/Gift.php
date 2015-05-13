<?php
class Df_PromoGift_Model_Resource_Gift extends Mage_Core_Model_Mysql4_Abstract {
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
		$this->_init(self::MAIN_TABLE, Df_PromoGift_Model_Gift::P__ID);
	}
	const _CLASS = __CLASS__;
	const MAIN_TABLE = 'df_promo_gift/gift';
	/**
	 * @see Df_PromoGift_Model_Gift::_construct()
	 * @see Df_PromoGift_Model_Resource_Gift_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_PromoGift_Model_Resource_Gift */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}