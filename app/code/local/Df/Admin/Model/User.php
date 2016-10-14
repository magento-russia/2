<?php
/**
 * @method Df_Admin_Model_Resource_User getResource()
 * @method Df_Admin_Model_User setNewPassword(string $value)
 * @method Df_Admin_Model_User setPasswordConfirmation(string $value)
 */
class Df_Admin_Model_User extends Mage_Admin_Model_User {
	/**
	 * @override
	 * @return Df_Admin_Model_Resource_User_Collection
	 */
	public function getResourceCollection() {return self::c();}

	/**
	 * @override
	 * @return Df_Admin_Model_Resource_User
	 */
	protected function _getResource() {return Df_Admin_Model_Resource_User::s();}

	/** @used-by Df_Admin_Model_Resource_User_Collection::_construct() */
	const _C = __CLASS__;

	/**
	 * @used-by Df_Cms_Helper_Data::getUsersArray()
	 * @return Df_Admin_Model_Resource_User_Collection
	 */
	public static function c() {return new Df_Admin_Model_Resource_User_Collection;}
	/**
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Admin_Model_User
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @used-by Df_Cms_Block_Admin_Page_Revision_Edit_Info::getAuthor()
	 * @param int|string $id
	 * @param string|null $field [optional]
	 * @return Df_Admin_Model_User
	 */
	public static function ld($id, $field = null) {return df_load(self::i(), $id, $field);}
}