<?php
class Df_Admin_Model_Resource_Role extends Mage_Admin_Model_Mysql4_Role {
	/** @used-by Df_AccessControl_Model_Setup_1_0_0::process() */
	const TABLE = 'admin/role';
	/** @return Df_Admin_Model_Resource_Role */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}