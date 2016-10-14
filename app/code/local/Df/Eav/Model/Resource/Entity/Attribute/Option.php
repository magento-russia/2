<?php
class Df_Eav_Model_Resource_Entity_Attribute_Option extends Mage_Eav_Model_Mysql4_Entity_Attribute_Option {
	/** @return Df_Eav_Model_Resource_Entity_Attribute_Option */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}


 