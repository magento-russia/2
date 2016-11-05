<?php
class Df_Admin_Model_Resource_User extends Mage_Admin_Model_Resource_User {
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}