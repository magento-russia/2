<?php
class Df_Admin_Model_Role extends Mage_Admin_Model_Role {
	/**
	 * @override
	 * @return Df_Admin_Model_Resource_Role_Collection
	 */
	public function getResourceCollection() {return self::c();}

	/**
	 * @override
	 * @return Df_Admin_Model_Resource_Role
	 * 2016-10-14
	 * В родительском классе метод переобъявлен через PHPDoc,
	 * и поэтому среда разработки думает, что он публичен.
	 */
	/** @noinspection PhpHierarchyChecksInspection */
	protected function _getResource() {return Df_Admin_Model_Resource_Role::s();}

	/** @used-by Df_Admin_Model_Resource_Role_Collection::_construct() */

	/**
	 * @used-by Df_AccessControl_Model_Role::P__ID
	 * @used-by Df_AccessControl_Model_Setup_1_0_0::process()
	 */
	const P__ID = 'role_id';

	/** @return Df_Admin_Model_Resource_Role_Collection */
	public static function c() {return new Df_Admin_Model_Resource_Role_Collection;}
}