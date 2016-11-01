<?php
namespace Df\YandexMarket;
class AddressSession extends \Df_Core_Model {
	/**
	 * @used-by Df_Checkout_Block_Frontend_Ergonomic_Address::addressFromYandexMarket()
	 * @param string $type
	 * @return array(string => string)
	 */
	public static function get($type) {
		df_param_string_not_empty($type, 0);
		return dfa(self::_get(), $type, array());
	}

	/**
	 * @used-by \Df\YandexMarket\Action\ImportAddress::_process()
	 * @param string $type
	 * @param array(string => string) $address
	 * @return void
	 */
	public static function set($type, array $address) {
		df_param_string_not_empty($type, 0);
		self::session()->setData(__CLASS__, array($type => $address) + self::_get());
	}

	/** @return array(array(string => string)) */
	private static function _get() {return df_nta(self::session()->getData(__CLASS__));}

	/** @return \Mage_Checkout_Model_Session */
	private static function session() {static $r; return $r ? $r : $r = df_session_checkout();}
}