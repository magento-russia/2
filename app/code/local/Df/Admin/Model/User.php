<?php
/**
 * @method Df_Admin_Model_Resource_User getResource()
 */
class Df_Admin_Model_User extends Mage_Admin_Model_User {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Admin_Model_Resource_User::mf());
	}
	const _CLASS = __CLASS__;
	const P__EMAIL = 'email';
	const P__FIRSTNAME = 'firstname';
	const P__IS_ACTIVE = 'is_active';
	const P__LASTNAME = 'lastname';
	const P__NEW_PASSWORD = 'new_password';
	const P__PASSWORD_CONFIRMATION = 'password_confirmation';
	const P__ROLE_ID = 'role_id';
	const P__USERNAME = 'username';
	/** @return Df_Admin_Model_Resource_User_Collection */
	public static function c() {return self::s()->getCollection();}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Admin_Model_User
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_Admin_Model_User
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
	/**
	 * @see Df_Admin_Model_Resource_User_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
	/** @return Df_Admin_Model_User */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}