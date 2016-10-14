<?php
class Df_Customer_Model_Resource_Address extends Mage_Customer_Model_Entity_Address {
	/** @return Df_Customer_Model_Resource_Address */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}